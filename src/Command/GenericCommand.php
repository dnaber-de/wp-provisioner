<?php # -*- coding: utf-8 -*-

namespace WpProvision\Command;

use WpProvision\Process\ProcessBuilder;

/**
 * Wrapper for any command
 *
 * @package WpProvision\Command
 */
final class GenericCommand implements Command  {

	/**
	 * @var ProcessBuilder
	 */
	private $process_builder;

	/**
	 * @param ProcessBuilder $process_builder
	 */
	public function __construct( ProcessBuilder $process_builder ) {

		$this->process_builder = $process_builder;
	}

	/**
	 * @param array $arguments
	 *
	 * @return string
	 */
	public function run( array $arguments = [] ) {

		$process = $this
			->process_builder
			->setArguments( $arguments )
			->setTimeout( null ) // no timeout
			->getProcess()
			->mustRun();

		return $process->getOutput();
	}

}
