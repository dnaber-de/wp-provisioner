<?php # -*- coding: utf-8 -*-

namespace WpProvision\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use WpProvision\Command\GenericCommand;
use WpProvision\Container\Configurator;
use WpProvision\Env\Shell;
use WpProvision\Exception\Factory\WpCliNotFound;

/**
 * Class WpCliCommandFactory
 *
 * @package WpProvision\Factory
 */
class WpCliCommandFactory {

	/**
	 * @var PhpExecutableFinder
	 */
	private $php_finder;

	/**
	 * @var ExecutableFinder
	 */
	private $exec_finder;

	/**
	 * @var Shell
	 */
	private $shell;

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param PhpExecutableFinder $php_finder
	 * @param ExecutableFinder $exec_finder
	 * @param Shell $shell
	 * @param ContainerInterface $container
	 */
	public function __construct(
		PhpExecutableFinder $php_finder,
		ExecutableFinder $exec_finder,
		Shell $shell,
		ContainerInterface $container
	) {

		$this->php_finder = $php_finder;
		$this->exec_finder = $exec_finder;
		$this->shell = $shell;
		$this->container = $container;
	}

	/**
	 * @param string $wp_cli
	 * @param string $cwd
	 *
	 * @return GenericCommand
	 */
	public function getWpCliCommand( $wp_cli = '', $cwd = '' ) {

		$wp_cli = (string) $wp_cli;
		$wp_cli or $wp_cli = 'wp';
		$cwd or $cwd = $this->shell->cwd();

		if ( $this->shell->commandExists( $wp_cli ) ) {
			return $this->buildCommand( [ $wp_cli ], $cwd );
		}

		// if the command points to an executable file
		if ( $this->shell->isReadable( $wp_cli ) && $this->shell->isExecutable( $wp_cli ) ) {
			return $this->buildCommand( [ $wp_cli ], $cwd );
		}

		$php_bin = $this->php_finder->find();
		// if it points to a file, assume it's a php script
		if ( $this->shell->isReadable( $wp_cli ) && $php_bin ) {
			return $this->buildCommand( [ $php_bin, $wp_cli ], $cwd );
		}

		// maybe fallback to 'wp'
		$wp_bin = $this->exec_finder->find( 'wp' );
		if ( $wp_bin ) {
			return $this->buildCommand( [ $wp_bin ], $cwd );
		}

		throw new WpCliNotFound( "Could not found command {$wp_cli}" );
	}

	private function buildCommand( array $base, $cwd ) {

		$provider = $this->container->get( Configurator::WP_CLI_PROCESS_BUILDER );
		// use set*() instead of with*() here, the instance is meant to be shared/mutable
		$provider->setPrefix( $base );
		$provider->setWorkingDirectory( $cwd );

		return new GenericCommand( $provider );
	}
}