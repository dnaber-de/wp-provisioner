<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

interface WpProvisioner {

	/**
	 * @return Versions
	 */
	public function versionList();

	/**
	 * @param $wp_dir
	 */
	public function setWpDir( $wp_dir );
}
