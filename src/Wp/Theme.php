<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

/**
 * Interface Theme
 *
 * @package WpProvision\Wp
 */
interface Theme {

	/**
	 * Todo: Consider implementing following methods:
	 *  delete()
	 *  get()
	 *  install()
	 *  is-installed()
	 *  list() (is a reserved keyword in PHP < 7)
	 *  path()
	 *  search()
	 *  status()
	 *  update()
	 */

	/**
	 * @param string $theme
	 *
	 * @return bool
	 */
	public function activate( $theme );

	/**
	 * @param string $theme
	 *
	 * @return bool
	 */
	public function disable( $theme, array $options = [] );

	/**
	 * @param string $theme
	 * @param array $options (Options: 'network' (bool), 'activate' (bool))
	 *
	 * @return mixed
	 */
	public function enable( $theme, array $options = [] );

	/**
	 * @return ThemeMod
	 */
	public function mod();
}