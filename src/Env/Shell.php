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

	/**
	 * Verify if a file exists and is executable
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	public function isExecutable( $file );
}
