<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\Command;

final class WpCliCli implements Cli {

	/**
	 * @var Command
	 */
	private $wp_cli;

	/**
	 * @param Command $wp_cli
	 */
	public function __construct( Command $wp_cli ) {

		$this->wp_cli = $wp_cli;
	}


	/**
	 * @return string
	 */
	public function version() {

		try {
			$version = $this->wp_cli->run( [ 'cli', 'version' ] );

			// Todo: use formatter
			return trim( $version );
		}  catch ( \Throwable $e ) {

			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
	}

	/**
	 * @return array
	 */
	public function info() {

		try {
			$info = $this->wp_cli->run( [ 'cli', 'info' ] );

			// Todo: Use formatter
			return explode( $info, PHP_EOL );
		}  catch ( \Throwable $e ) {

			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
	}

}