<?php # -*- coding: utf-8 -*-

namespace WpProvision\Utils;

/**
 * Interface PasswordGenerator
 *
 * @package WpProvision\Utils
 */
interface PasswordGenerator {

	/**
	 * @return string
	 */
	public function generatePassword();
}
