<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use WpProvision\Wp\Cli;
use WpProvision\Wp\Core;
use WpProvision\Wp\Db;
use WpProvision\Wp\Plugin;
use WpProvision\Wp\SearchReplace;
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
	 * @return Db
	 */
	public function db();

	/**
	 * @return Plugin
	 */
	public function plugin();

	/**
	 * @return SearchReplace
	 */
	public function searchReplace();

	/**
	 * @return Site
	 */
	public function site();

	/**
	 * @return User
	 */
	public function user();
}
