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

		// Todo: Use more poratble `command -v` see http://stackoverflow.com/a/4785518/2169046
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

	/**
	 * @param $file
	 *
	 * @return bool
	 */
	public function isReadable( $file ) {

		return is_readable( $file );
	}

	/**
	 * @return string
	 */
	public function cwd() {

		return getcwd();
	}

}
