<?php # -*- coding: utf-8 -*-

namespace WpProvision\Command;

/**
 * Interface Command
 *
 * Wraps a command (like `$ wp`) so you have to deal only with the arguments
 *
 * @package WpProvision\Command
 */
interface Command {

	/**
	 * @param array $arguments
	 *
	 * @return string
	 */
	public function run( array $arguments = [] );
}
