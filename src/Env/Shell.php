<?php # -*- coding: utf-8 -*-

namespace WpProvision\Env;

/**
 * Interface Shell
 *
 * @package WpProvision\Env
 */
interface Shell {

	/**
	 * @param $command
	 *
	 * @return bool
	 */
	public function commandExists( $command );
}
