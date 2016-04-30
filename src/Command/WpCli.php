<?php # -*- coding: utf-8 -*-

namespace WpProvision\Command;

use
	WpProvision\Env;

/**
 * Wrapper for WP-CLI command
 *
 * Usage example: To execute `wp site list` just run
 *
 * ( new WpCli() )->run( 'site list' );
 *
 * @package WpProvision\Command
 */
class WpCli implements SubCommand {

	private $base;

	private $bin_path;

	private $shell;

	/**
	 * @param Env\Shell $shell
	 * @param string $bin_path
	 */
	public function __construct( Env\Shell $shell, $bin_path = '' ) {

		$this->shell = $shell;
		if ( $this->bin_path ) {
			$this->bin_path = realpath( $bin_path );
			$this->base = $bin_path;
		} else {
			$this->base = 'wp';
		}
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

		if ( $this->bin_path ) {
			return file_exists( $this->bin_path )
				&& is_executable( $this->bin_path );
		};

		return $this->shell->commandExists( $this->base );
	}

	public function run( $command ) {
		// TODO: Implement run() method.
	}

}
