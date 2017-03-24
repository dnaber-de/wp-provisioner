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

		$args = [
			'where',
			$command
		];
		$output = $this
			->process_builder
			->setArguments( $args )
			->getProcess()
			->mustRun()
			->getOutput();

		$output = explode( PHP_EOL, trim( $output ) );
		foreach ( $output as $path ) {
			if ( $this->isReadable( trim( $path ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Verify if a file exists and is executable
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	public function isExecutable( $file ) {

		$executable_extensions = [ 'exe', 'bat', 'cmd', 'com' ];

		$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

		return in_array( $extension, $executable_extensions );
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