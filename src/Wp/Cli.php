<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

interface Cli {

	/**
	 * @return string
	 */
	public function version();

	/**
	 * @return array
	 */
	public function info();
}