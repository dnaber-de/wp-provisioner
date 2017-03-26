<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

/**
 * Interface Configurator
 *
 * @package WpProvision\Container
 */
interface Configurator {

	const WP_CLI_PROCESS_BUILDER = '$wpCliProcessBuilder';
	const PROCESS_BUILDER = '$processBuilder';
	const WP_CLI_COMMAND = '$wpCliCommand';

	/**
	 * @param DiceConfigurable $dice
	 */
	public function configure( DiceConfigurable $dice );
}