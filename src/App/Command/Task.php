<?php # -*- coding: utf-8 -*-

namespace WpProvision\App\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Task
 *
 * @package WpProvision\App\Command
 */
class Task extends Command {

	const COMMAND_NAME = 'task';

	const ARGUMENT_FILE = 'file';

	const OPTION_WP_DIR = 'wp-dir';
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
			->setDescription( 'Executes a task file' )
			->addArgument(
				self::ARGUMENT_FILE,
				InputArgument::REQUIRED,
				'The task file to execute',
				null
			)
			->addOption(
				self::OPTION_WP_DIR,
				null,
				InputOption::VALUE_OPTIONAL,
				'WordPress directory',
				NULL
			)
			->addOption(
				self::OPTION_WP_CLI,
				null,
				InputOption::VALUE_OPTIONAL,
				'WP-CLI binary',
				'wp'
			);
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 */
	protected function execute( InputInterface $input, OutputInterface $output ) {

		$output->writeln( "Command under construction" );

		return 1;
	}

}