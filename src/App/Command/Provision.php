<?php # -*- coding: utf-8 -*-

namespace WpProvision\App\Command;

use Psr\Container\ContainerInterface;
use WpProvision\Api\IsolatedVersions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use LogicException;
use WpProvision\Api\SymfonyOutputAdapter;
use WpProvision\Api\WpCliCommandProvider;
use WpProvision\Command\WpCli;
use WpProvision\Command\WpCliCommand;
use WpProvision\Exception\Api\TaskFileNotFound;
use WpProvision\Exception\Api\TaskFileReturnsNoCallable;
use WpProvision\Exception\App\Argument\WpCliNotExecutable;
use WpProvision\Exception\App\Argument\WpDirectoryNotFound;
use WpProvision\Process\SymfonyProcessBuilderAdapter;

/**
 * Class Provision
 *
 * @package WpProvision\App\Command
 */
class Provision extends Command {

	const COMMAND_NAME = 'provision';

	const ARGUMENT_VERSION = 'version';

	const OPTION_ISOLATION = 'isolation';
	const OPTION_WP_DIR = 'wp-dir';
	const OPTION_TASK_FILE = 'file';
	const OPTION_WP_CLI = 'wp-cli';

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(
		ContainerInterface $container
	) {

		$this->container = $container;

		parent::__construct();
	}

	/**
	 * Configures the current command.
	 */
	protected function configure() {

		$this
			->setName( self::COMMAND_NAME )
			->setDescription( 'Runs the provision routines of a given version' )
			->addArgument( self::ARGUMENT_VERSION, InputArgument::REQUIRED, 'The version to run provisions for' )
			->addOption(
				self::OPTION_ISOLATION,
				NULL,
				InputOption::VALUE_OPTIONAL,
				'Skip all version provisioning routines prior the given version',
				FALSE
			)
			->addOption(
				self::OPTION_WP_DIR,
				'w',
				InputOption::VALUE_OPTIONAL,
				'WP install directory',
				NULL
			)
			->addOption(
				self::OPTION_TASK_FILE,
				'f',
				InputOption::VALUE_OPTIONAL,
				'Task file that defines the version',
				NULL
			)
			->addOption(
				self::OPTION_WP_CLI,
				NULL,
				InputOption::VALUE_OPTIONAL,
				'Path to WP-CLI executable',
				'wp'
			);
	}

	/**
	 * Executes the current command.
	 *
	 * This method is not abstract because you can use this class
	 * as a concrete class. In this case, instead of defining the
	 * execute() method, you set the code to execute by passing
	 * a Closure to the setCode() method.
	 *
	 * @param InputInterface  $input  An InputInterface instance
	 * @param OutputInterface $output An OutputInterface instance
	 *
	 * @return null|int null or 0 if everything went fine, or an error code
	 *
	 * @throws LogicException When this abstract method is not implemented
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {

		$version_api = $this->container->get( IsolatedVersions::class );
		$version = $input->getArgument( self::ARGUMENT_VERSION );
		/* @var WpCliCommand $wp_cli */
		$wp_cli = $this->container->get( WpCli::class );

		$wp_cli->setWpInstallDir( $this->getWpDir( $input ) );
		$wp_cli->setWpCliBinary( $this->getWpCliBinary( $input ) );

		if ( ! $wp_cli->commandExists() ) {
			throw new WpCliNotExecutable( "Command: {$wp_cli->base()}");
		}

		$task_file = $this->getTaskFile( $input );
		if ( ! is_readable( $task_file ) ) {
			throw new TaskFileNotFound( "File {$task_file} not found or no readable" );
		}

		$tasks = require_once $task_file;
		if ( ! is_callable( $tasks ) ) {
			throw new TaskFileReturnsNoCallable( "File {$task_file}" );
		}

		$tasks(
			$this->container->get( IsolatedVersions::class ),
			$this->container->get( WpCliCommandProvider::class ),
			new SymfonyOutputAdapter( $output )
		);
		if ( ! $version_api->versionExists( $version ) ) {
			$output->writeln( "<error>Error: no provision for {$version} defined</error>" );
			return 1;
		}

		$isolation = (bool) $input->getOption( self::OPTION_ISOLATION );
		$version_api->executeProvision( $version, $isolation );

		return 0;
	}

	/**
	 * @param InputInterface $input
	 *
	 * @return string
	 */
	private function getWpDir( InputInterface $input ) {

		$wp_dir = $input->getOption( self::OPTION_WP_DIR );
		$wp_dir and $wp_dir = realpath( $wp_dir );
		$wp_dir or $wp_dir = getcwd();

		if ( ! is_string( $wp_dir ) || ! is_dir( $wp_dir ) ) {
			throw new WpDirectoryNotFound();
		}

		return $wp_dir;
	}

	/**
	 * @param InputInterface $input
	 *
	 * @return string
	 */
	private function getTaskFile( InputInterface $input ) {

		$file = $input->getOption( self::OPTION_TASK_FILE );
		if ( $file && ! is_readable( $file ) ) {
			throw new TaskFileNotFound( "File: {$file}");
		}

		if ( ! $file ) {
			$wd = getcwd();
			$wd and $file = "{$wd}/provision.php";
		}
		if ( ! $file && ! is_readable( $file ) ) {
			throw new TaskFileNotFound( "File: {$file}" );
		}

		return $file;
	}

	/**
	 * @param InputInterface $input
	 *
	 * @return string
	 */
	private function getWpCliBinary( InputInterface $input ) {

		$wp = $input->getOption( self::OPTION_WP_CLI );

		return $wp;
	}
}
