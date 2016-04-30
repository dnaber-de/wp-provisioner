<?php # -*- coding: utf-8 -*-

namespace WpProvision\Command;

/**
 * Interface SubCommand
 *
 * @package WpProvision\Command
 */
interface SubCommand {

	/**
	 * @return string
	 */
	public function base();

	/**
	 * @return bool
	 */
	public function commandExists();

	/**
	 * @param string $command
	 *
	 * @return string
	 */
	public function run( $command );
}
