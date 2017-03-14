<?php # -*- coding: utf-8 -*-

namespace WpProvision\Env;

use WpProvision\Process\ProcessBuilder;

/**
 * Class Bash
 *
 * @package WpProvision\Env
 */
final class Bash implements Shell {

	/**
	 * @var ProcessBuilder
	 */
	private $processBuilder;

	/**
	 * @param ProcessBuilder $processBuilder
	 */
	public function __construct( ProcessBuilder $processBuilder ) {

		$this->processBuilder = $processBuilder;
	}

	/**
	 * @param $command
	 *
	 * @return bool
	 */
	public function commandExists( $command ) {

		$process = $this
			->processBuilder
			->setArguments(
				[
					'hash',
					$command,
					'2>/dev/null || echo "false"'
				]
			)
			->getProcess();

		$output = $process
			->mustRun()
			->getOutput();

		return "false" !== trim( $output );
	}

	/**
	 * Verify if a file exists and is executable
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	public function isExecutable( $file ) {

		return file_exists( $file ) && is_executable( $file );
	}

}
