<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\WpCliCommand;

final class WpCliCli implements Cli {

	/**
	 * @var WpCliCommand
	 */
	private $wp_cli;

	/**
	 * @param WpCliCommand $wp_cli
	 */
	public function __construct( WpCliCommand $wp_cli ) {

		$this->wp_cli = $wp_cli;
	}


	/**
	 * @return string
	 */
	public function version() {

		try {
			$version = $this->wp_cli->run( [ 'cli', 'version' ] );
			return trim( $version );
		} catch( \Exception $e ) {
			return '';
		}
	}

	/**
	 * @return array
	 */
	public function info() {

		try {
			$info = $this->wp_cli->run( [ 'cli', 'info' ] );
			return explode( $info, PHP_EOL );
		} catch( \Exception $e ) {
			return [];
		}
	}

}