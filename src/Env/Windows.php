<?php # -*- coding: utf-8 -*-

namespace WpProvision\Env;

use WpProvision\Process\ProcessBuilder;

/**
 * Class Windows
 *
 * @package WpProvision\Env
 */
final class Windows implements Shell {

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
	 * @param $command
	 *
	 * @return bool
	 */
	public function commandExists( $command ) {

		//Todo: use `where $command`, see http://stackoverflow.com/a/18540185/2169046
	}

	/**
	 * Verify if a file exists and is executable
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	public function isExecutable( $file ) {
		// TODO: Implement isExecutable() method.
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