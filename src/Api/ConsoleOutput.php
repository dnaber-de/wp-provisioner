<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

/**
 * Interface ConsoleOutput
 *
 * @package WpProvision\Api
 */
interface ConsoleOutput {

	/**
	 * @param array $messages
	 * @param bool $newline
	 *
	 * @return void
	 */
	public function write( array $messages, $newline = false );

	/**
	 * @param array $messages
	 *
	 * @return void
	 */
	public function writeln( array $messages );
}