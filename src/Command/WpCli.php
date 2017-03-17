<?php # -*- coding: utf-8 -*-

namespace WpProvision\Command;

use WpProvision\Env\Shell;
use WpProvision\Process\ProcessBuilder;
use WpProvision\Process\SymfonyProcessBuilderAdapter;
use LogicException;

/**
 * Wrapper for WP-CLI command
 *
 * Usage example: To execute `wp site list` just run
 *
 * ( new WpCli() )->run( 'site list' );
 *
 * @package WpProvision\Command
 */
final class WpCli implements WpCliCommand {

	/**
	 * @var string
	 */
	private $base;

	/**
	 * @var Shell
	 */
	private $shell;

	/**
	 * @var ProcessBuilder
	 */
	private $process_builder;

	/**
	 * @param Shell $shell
	 * @param ProcessBuilder $process_builder
	 */
	public function __construct(
		Shell $shell,
		ProcessBuilder $process_builder
	) {

		$this->shell = $shell;
		$this->base = 'wp';
		$this->process_builder = $process_builder->withPrefix( [ $this->base ] );
	}

	/**
	 * @return string
	 */
	public function base() {

		return $this->base;
	}

	/**
	 * @return bool
	 */
	public function commandExists() {

		return $this->shell->commandExists( $this->base );
	}

	/**
	 * @param string $dir
	 *
	 * @return void
	 */
	public function setWpInstallDir( $dir ) {

		$this->process_builder = $this->process_builder->withWorkingDirectory( $dir );
	}

	/**
	 * @param string $file
	 *
	 * @return void
	 */
	public function setWpCliBinary( $file ) {

		$this->process_builder = $this->process_builder->withPrefix( [ $file ] );
	}

	/**
	 * @param array $arguments
	 *
	 * @return string
	 */
	public function run( array $arguments = [] ) {

		if ( ! $this->commandExists() )
			throw new LogicException( "The base command {$this->base()} does not exists or is not executable." );

		$process = $this
			->process_builder
			->setArguments( [] ) // reset the process builder state
			->setArguments( $arguments )
			->getProcess()
			->mustRun();

		return $process->getOutput();
	}

}
