<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use
	WpProvision\Wp;

/**
 * Interface WpCommandProvider
 *
 * @package WpProvision\Api
 */
interface WpCommandProvider {

	/**
	 * @return Wp\Core
	 */
	public function core();

	/**
	 * @return Wp\Plugin
	 */
	public function plugin();

	/**
	 * @return Wp\Site
	 */
	public function site();

	/**
	 * @return Wp\User
	 */
	public function user();
}
