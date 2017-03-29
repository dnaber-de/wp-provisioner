<?php # -*- coding: utf-8 -*-

namespace WpProvision\App\Command;

use WpProvision\Api\SymfonyOutputAdapter;
use WpProvision\Api\WpCliCommandProvider;
use WpProvision\App\Command\Command as ApplicationCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WpProvision\Container\Configurator;
use WpProvision\Container\DiceApiConfigurator;
use WpProvision\Container\DiceConfigurable;
use WpProvision\Env\Shell;
use WpProvision\Exception\Api\TaskFileNotFound;
use WpProvision\Exception\Api\TaskFileReturnsNoCallable;

/**
 * Class Task
 *
 * @package WpProvision\App\Command
 */
class Task extends Command implements ApplicationCommand {

	const COMMAND_NAME = 'task';
	const ARGUMENT_FILE = 'file';

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var DiceConfigurable
	 */
	private $dice;

	private $shell;

	/**
	 * @var Configurator
	 */
	private $configurator;

	/**
	 * @param ContainerInterface $container
	 * @param DiceConfigurable $dice
	 * @param Configurator $configurator
	 */
	public function __construct(
		ContainerInterface $container,
		DiceConfigurable $dice,
		Shell $shell, // Todo: move to container
		Configurator $configurator = null
	) {

		$this->container = $container;
		$this->dice = $dice;
		$this->shell = $shell;
		$this->configurator = $configurator ?: null;

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
				null
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

		$this->configurator or $this->configurator = new DiceApiConfigurator( $this->container, $input );
		$this->configurator->configure( $this->dice );

		$task_file = $this->getTaskFile( $input );
		$tasks = require_once $task_file;
		if ( ! is_callable( $tasks ) ) {
			throw new TaskFileReturnsNoCallable( "File {$task_file}" );
		}


		return (int) $tasks(
			$this->container->get( WpCliCommandProvider::class ),
			new SymfonyOutputAdapter( $output )
		);
	}

	/**
	 * @param InputInterface $input
	 *
	 * @return string
	 */
	private function getTaskFile( InputInterface $input ) {

		$file = $input->getArgument( self::ARGUMENT_FILE );
		if ( ! $this->shell->isReadable( $file ) ) {
			throw new TaskFileNotFound( "File: {$file}");
		}

		return $file;
	}
}