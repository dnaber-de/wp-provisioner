<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

/**
 * Interface ThemeMod
 *
 * @package WpProvision\Wp
 */
interface ThemeMod {

	/**
	 * @param array $mods
	 * @param bool $all
	 * @param array $options
	 *
	 * @return array
	 */
	public function get( array $mods = [], $all = false, array $options = [] );

	/**
	 * @param array $mods
	 * @param bool $all
	 *
	 * @return bool
	 */
	public function remove( array $mods = [], $all = false );

	/**
	 * @param string $mod
	 * @param string $value
	 *
	 * @return bool
	 */
	public function set( $mod, $value );
}