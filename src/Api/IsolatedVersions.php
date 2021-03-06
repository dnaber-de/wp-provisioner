<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use LogicException;

/**
 * Class IsolatedVersions
 *
 * Minimal implementation of the version list. This does not resolve consecutive version numbers (as planned
 * in the API concept)
 *
 * @package WpProvision\Api
 */
final class IsolatedVersions implements Versions {

	/**
	 * @var array
	 */
	private $versions = [];

	/**
	 * @var WpCommandProvider
	 */
	private $provider;

	/**
	 * @param WpCommandProvider $provider
	 */
	public function __construct( WpCommandProvider $provider ) {

		$this->provider = $provider;
	}

	/**
	 * @param string $version
	 *
	 * @return bool
	 */
	public function versionExists( $version ) {

		return isset( $this->versions[ $version ] );
	}

	/**
	 * @param string $version
	 * @param bool   $isolation
	 *
	 * @return bool
	 */
	public function executeProvision( $version, $isolation = false ) {

		if ( ! $this->versionExists( $version ) ) {
			throw new LogicException( "No provisioner registered for version '{$version}''" );
		}

		return (bool) call_user_func_array( $this->versions[ $version ], [ $this->provider ] );
	}

	/**
	 * Register a provision routine
	 *
	 * @param string   $version
	 * @param callable $callback
	 * @param bool     $isolation
	 *
	 * @return bool
	 */
	public function addProvision( $version, callable $callback, $isolation = false ) {

		if ( $this->versionExists( $version ) ) {
			throw new LogicException( "Version '{$version}' already exists" );
		}

		$this->versions[ $version ] = $callback;
	}

}
