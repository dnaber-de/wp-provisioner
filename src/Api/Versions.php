<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

/**
 * Interface Versions
 *
 * @package WpProvision\Api
 */
interface Versions {

	/**
	 * @param string $version
	 *
	 * @return bool
	 */
	public function versionExists( $version );

	/**
	 * @param string $version
	 * @param bool   $isolation
	 *
	 * @return bool
	 */
	public function executeProvision( $version, $isolation = false );

	/**
	 * Register a provision routine
	 *
	 * @param string   $version
	 * @param callable $callback
	 * @param bool     $isolation
	 *
	 * @return bool
	 */
	public function addProvision( $version, callable $callback, $isolation = false );
}
