<?php # -*- coding: utf-8 -*-

namespace WpProvision\App\Command;

use
	WpProvision\Api,
	Symfony\Component\Console\Command as SymfonyCommand,
	Symfony\Component\Console\Input as SymfonyInput,
	Symfony\Component\Console\Output as SymfonyOutput,
	LogicException;

/**
 * Class Provision
 *
 * @package WpProvision\App\Command
 */
class Provision extends SymfonyCommand\Command {

	const ARGUMENT_VERSION = 'version';
	const OPTION_ISOLATION = 'isolation';

	/**
	 * @var Api\Versions
	 */
	private $versions;

	/**
	 * @param Api\Versions $versions
	 * @param string       $name
	 */
	public function __construct( Api\Versions $versions, $name = NULL ) {

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
			->addArgument( self::ARGUMENT_VERSION, SymfonyInput\InputArgument::REQUIRED, 'The version to run provisions for' )
			->addOption(
				self::OPTION_ISOLATION,
				NULL,
				SymfonyInput\InputOption::VALUE_OPTIONAL,
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
	 * @param SymfonyInput\InputInterface  $input  An InputInterface instance
	 * @param SymfonyOutput\OutputInterface $output An OutputInterface instance
	 *
	 * @return null|int null or 0 if everything went fine, or an error code
	 *
	 * @throws LogicException When this abstract method is not implemented
	 */
	protected function execute( SymfonyInput\InputInterface $input, SymfonyOutput\OutputInterface $output ) {

		$version = $input->getArgument( self::ARGUMENT_VERSION );
		if ( ! $this->versions->versionExists( $version ) ) {
			$output->writeln( "<error>Error: no provision for {$version} defined</error>" );
		}

		$isolation = (bool) $input->getOption( self::OPTION_ISOLATION );
		$this->versions->executeProvision( $version, $isolation );
	}

}
