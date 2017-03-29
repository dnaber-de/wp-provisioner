<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use WpProvision\Api\IsolatedVersions;
use WpProvision\Api\WpCliCommandProvider;
use WpProvision\Api\WpCommandProvider;
use WpProvision\Command\Command;
use WpProvision\Env\Bash;
use WpProvision\Env\Shell;
use WpProvision\Env\Windows;
use WpProvision\Exception\App\Argument\WpDirectoryNotFound;
use WpProvision\Exception\Env\CurrentWorkingDirectoryNotFound;
use WpProvision\Factory\WpCliCommandFactory;
use WpProvision\Process\ProcessBuilder;
use WpProvision\Process\SymfonyProcessBuilderAdapter;
use WpProvision\Utils\PasswordGenerator;
use WpProvision\Utils\Sha1PasswordGenerator;
use WpProvision\Wp\Db;
use WpProvision\Wp\Plugin;
use WpProvision\Wp\Site;
use WpProvision\Wp\User;
use WpProvision\Wp\WpCliCli;
use WpProvision\Wp\WpCliCore;
use WpProvision\Wp\WpCliDb;
use WpProvision\Wp\WpCliPlugin;
use WpProvision\Wp\WpCliSite;
use WpProvision\Wp\WpCliUser;

/**
 * Class DiceApiConfigurator
 *
 * @package WpProvision\Container
 */
final class DiceApiConfigurator implements Configurator {

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * Shared instance of WP CLI command
	 *
	 * @var Command
	 */
	private $wp_cli_command;

	/**
	 * @param ContainerInterface $container
	 * @param InputInterface $input
	 */
	public function __construct( ContainerInterface $container, InputInterface $input ) {

		$this->container = $container;
		$this->input = $input;
	}

	/**
	 * @param DiceConfigurable $dice
	 */
	public function configure( DiceConfigurable $dice ) {

		// Shared inside all WP-CLI sub-commands
		$dice->addRule(
			self::WP_CLI_PROCESS_BUILDER,
			[
				'shared' => true,
				'instanceOf' => SymfonyProcessBuilderAdapter::class
			]
		);

		// independent process builder
		$dice->addRule(
			self::PROCESS_BUILDER,
			[
				'shared' => false,
				'instanceOf' => SymfonyProcessBuilderAdapter::class
			]
		);

		//Environment
		$dice->addRule(
			Windows::class,
			[
				'shared' => true,
				'substitutions' => [
					ProcessBuilder::class => self::PROCESS_BUILDER
				]
			]
		);
		$dice->addRule(
			Bash::class,
			[
				'shared' => true,
				'substitutions' => [
						ProcessBuilder::class => self::PROCESS_BUILDER
				]
			]
		);

		$dice->addRule(
			WpCliCommandFactory::class,
			[
				'shared' => true,
				'substitutions' => [
					Shell::class => [
						'instance' => $this->shellSubstitution()
					],
					ContainerInterface::class => [
						'instance' => function() {
							return $this->container;
						}
					]
				]
			]
		);

		$dice->addRule(
			IsolatedVersions::class,
			[
				'shared' => true,
				'substitutions' => [
					WpCommandProvider::class => [
						'instance' => WpCliCommandProvider::class
					]
				]
			]
		);
		$dice->addRule(
			WpCliCommandProvider::class,
			[
				'shared' => true,
				'substitutions' => [
					ContainerInterface::class => [
						'instance' => function() {
							return $this->container;
						}
					]
				]
			]
		);


		/**
		 * Todo
		 * This doesn't work right now,
		 * as we can't fetch the instance via a Factory.
		 * If https://github.com/Level-2/Dice/issues/128
		 * is solved we can continue to share self::WP_CLI_COMMAND
		 * instance.
		 */
		/*
		$dice->addRule(
			self::WP_CLI_COMMAND,
			[
				'shared' => true,
				'instanceOf' => GenericCommand::class,
				'substitutions' => [
					Shell::class => [
						'instance' => $this->shellSubstitution()
					],
					ProcessBuilder::class => [
						'instance' => self::WP_CLI_PROCESS_BUILDER
					]
				],
				'constructParams' => [ 'wp' ],
				// 'instance' => factory goes here…
			]
		);
		*/

		$commands = [
			WpCliCli::class,
			WpCliCore::class,
			WpCliDb::class,
			WpCliPlugin::class,
			WpCliSite::class,
			WpCliUser::class,
		];
		foreach ( $commands as $class ) {
			$dice->addRule(
				$class,
				[
					'substitutions' => [
						Command::class => [
							/**
							 * Todo: See comment above, can be replaced with self::WP_CLI_COMMAND
							 */
							'instance' => $this->wpCliCommandSubstitution()
						],
						User::class => [
							'instance' => WpCliUser::class
						],
						Plugin::class => [
							'instance' => WpCliPlugin::class
						],
						Site::class => [
							'instance' => WpCliSite::class
						],
						Db::class => [
							'instance' => WpCliDb::class,
						],
						PasswordGenerator::class => [
							'instance' => Sha1PasswordGenerator::class
						]
					]
				]
			);
		}
	}

	private function wpCliCommandSubstitution() {

		return function() {
			if ( ! $this->wp_cli_command ) {
				$this->wp_cli_command = $this->container
					->get( WpCliCommandFactory::class )
					->getWpCliCommand(
						$this->getWpCliBin(),
						$this->getCwd()
					);
			}

			return $this->wp_cli_command;
		};
	}

	private function shellSubstitution() {

		return function() {
			return 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) )
				? $this->container->get( Windows::class )
				: $this->container->get( Bash::class );
		};
	}

	/**
	 * Todo: Exclude it to Argument\* domain
	 *
	 * @return string
	 */
	private function getWpCliBin() {

		$wp = $this->input->getOption( \WpProvision\App\Command\Command::OPTION_WP_CLI );

		if ( ! $wp ) {
			return 'wp';
		}

		$cli_is_file = realpath( $wp );
		if ( false === $cli_is_file ) {
			// Todo: Check if Command exists here
			return $wp;
		}

		// Todo: Check if file exists and is executable here
		return $cli_is_file;
	}

	/**
	 * Todo: Exclude this to Argument\* domain
	 *
	 * @return string
	 */
	private function getCwd() {

		$wp_dir = $this->input->getOption( \WpProvision\App\Command\Command::OPTION_WP_DIR );
		$wp_dir and $wp_dir = realpath( $wp_dir );
		if ( false === $wp_dir ) {
			throw new WpDirectoryNotFound();
		}

		$wp_dir or $wp_dir = getcwd();
		if ( ! is_string( $wp_dir ) || ! is_dir( $wp_dir ) ) {
			throw new CurrentWorkingDirectoryNotFound();
		}

		return $wp_dir;
	}

}