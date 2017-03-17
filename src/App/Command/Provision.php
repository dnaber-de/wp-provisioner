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
use WpProvision\Exception\Api\TaskFileNotFound;
use WpProvision\Exception\Api\TaskFileReturnsNoCallable;
use WpProvision\Process\SymfonyProcessBuilderAdapter;

/**
 * Class Provision
 *
 * @package WpProvision\App\Command
 */
class Provision extends Command {

	const ARGUMENT_VERSION = 'version';
	const OPTION_ISOLATION = 'isolation';
	const COMMAND_NAME = 'provision';

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


		$cwd = realpath( getcwd() );
		// Todo: Make that variable!
		$provison_file = $cwd . '/provision.php';
		if ( ! is_readable( $provison_file ) ) {
			throw new TaskFileNotFound( "File {$provison_file} not found or no readable" );
		}

		$tasks = require_once $provison_file;
		if ( ! is_callable( $tasks ) ) {
			throw new TaskFileReturnsNoCallable( "File {$provison_file}" );
		}

		// Todo: make that variable
		$this->container
			->get( SymfonyProcessBuilderAdapter::class )
			->setWorkingDirectory( $cwd );

		//Todo make WP CLI binary variable
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

}
