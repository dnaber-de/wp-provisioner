<?php # -*- coding: utf-8 -*-

namespace WpProvision\Command;

/**
 * Interface WpCliCommand
 *
 * Semantic interface for a WpCli base command. It must accept all sub-commands of
 * WP-CLI listed in @link http://wp-cli.org/commands/
 *
 * @package WpProvision\Command
 */
interface WpCliCommand extends BaseCommand {

	/**
	 * @param string $dir
	 *
	 * @return void
	 */
	public function setWpInstallDir( $dir );

	/**
	 * @param string $file
	 *
	 * @return void
	 */
	public function setWpCliBinary( $file );
}
