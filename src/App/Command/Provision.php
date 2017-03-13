<?php # -*- coding: utf-8 -*-

namespace WpProvision\App\Command;

use WpProvision\Api\Versions;
use Symfony\Component\Console\Command\Command;
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
class Provision extends Command  {

	const ARGUMENT_VERSION = 'version';
	const OPTION_ISOLATION = 'isolation';

	/**
	 * @var Versions
	 */
	private $versions;

	/**
	 * @param Versions $versions
	 * @param string       $name
	 */
	public function __construct( Versions $versions, $name = NULL ) {

		$this->versions = $versions;
		parent::__construct( $name );
	}

	/**
	 * Configures the current command.
	 */
	protected function configure() {

		$this
			->setName( 'provision' )
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

		$version = $input->getArgument( self::ARGUMENT_VERSION );
		if ( ! $this->versions->versionExists( $version ) ) {
			$output->writeln( "<error>Error: no provision for {$version} defined</error>" );
		}

		$isolation = (bool) $input->getOption( self::OPTION_ISOLATION );
		$this->versions->executeProvision( $version, $isolation );

		return 0;
	}

}
