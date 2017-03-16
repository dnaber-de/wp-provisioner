<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

/**
 * Interface Configurator
 *
 * @package WpProvision\Container
 */
interface Configurator {

	/**
	 * @param string $file
	 */
	public function setWpCliExecutable( $file );
}