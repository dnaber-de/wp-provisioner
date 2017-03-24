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
	private $process_builder;

	/**
	 * @param ProcessBuilder $processBuilder
	 */
	public function __construct( ProcessBuilder $processBuilder ) {

		$this->process_builder = $processBuilder;
	}

	/**
	 * @param $command
	 *
	 * @return bool
	 */
	public function commandExists( $command ) {


		$args = [
			'command',
			'-v',
			$command,
			'2>/dev/null 2>&1 || echo "false"'
		];
		$output = $this
			->process_builder
			->setArguments( $args )
			->getProcess()
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
