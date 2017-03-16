<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

/**
 * Interface DiceConfigurable
 *
 * @package WpProvision\Container
 */
interface DiceConfigurable {

	/**
	 * @param string $id
	 * @param array $rule
	 *
	 * @return void
	 */
	public function addRule( $id, array $rule );

	/**
	 * @param string $id
	 *
	 * @return array
	 */
	public function getRule( $id );
}