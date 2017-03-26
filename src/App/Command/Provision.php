<?php # -*- coding: utf-8 -*-

namespace WpProvision\App\Command;

use WpProvision\Api\SymfonyOutputAdapter;
use WpProvision\Api\WpCliCommandProvider;
use WpProvision\Container\Configurator;
use WpProvision\Container\DiceApiConfigurator;
use WpProvision\Container\DiceConfigurable;
use WpProvision\Exception\Api\TaskFileNotFound;
use WpProvision\Exception\Api\TaskFileReturnsNoCallable;
use WpProvision\App\Command\Command as ApplicationCommand;
use WpProvision\Api\IsolatedVersions;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use LogicException;

/**
 * Class Provision
 *
 * @package WpProvision\App\Command
 */
class Provision extends SymfonyCommand implements ApplicationCommand {

	const COMMAND_NAME = 'provision';

	const ARGUMENT_VERSION = 'version';

	const OPTION_ISOLATION = 'isolation';
	const OPTION_TASK_FILE = 'file';

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var DiceConfigurable
	 */
	private $dice;

	/**
	 * @var Configurator
	 */
	private $configurator;

	/**
	 * @param ContainerInterface $container
	 * @param DiceConfigurable $dice
	 */
	public function __construct(
		ContainerInterface $container,
		DiceConfigurable $dice,
		Configurator $configurator = null
	) {

		$this->container = $container;
		$this->dice = $dice;
		$this->configurator = $configurator;

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
				null,
				InputOption::VALUE_OPTIONAL,
				'Skip all version provisioning routines prior the given version',
				false
			)
			->addOption(
				self::OPTION_WP_DIR,
				'w',
				InputOption::VALUE_OPTIONAL,
				'WP install directory',
				null
			)
			->addOption(
				self::OPTION_TASK_FILE,
				'f',
				InputOption::VALUE_OPTIONAL,
				'Task file that defines the version',
				null
			)
			->addOption(
				self::OPTION_WP_CLI,
				null,
				InputOption::VALUE_OPTIONAL,
				'Path to WP-CLI executable',
				null
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
		$this->configurator or $this->configurator = new DiceApiConfigurator( $this->container, $input );
		$this->configurator->configure( $this->dice );

		$version_api = $this->container->get( IsolatedVersions::class );
		$version = $input->getArgument( self::ARGUMENT_VERSION );

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
}
