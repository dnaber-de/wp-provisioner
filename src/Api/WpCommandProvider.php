<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use WpProvision\Wp\Cli;
use WpProvision\Wp\Core;
use WpProvision\Wp\Plugin;
use WpProvision\Wp\Site;
use WpProvision\Wp\User;

/**
 * Interface WpCommandProvider
 *
 * @package WpProvision\Api
 */
interface WpCommandProvider {

	/**
	 * @return Cli
	 */
	public function cli();

	/**
	 * @return Core
	 */
	public function core();

	/**
	 * @return Plugin
	 */
	public function plugin();

	/**
	 * @return Site
	 */
	public function site();

	/**
	 * @return User
	 */
	public function user();
}
