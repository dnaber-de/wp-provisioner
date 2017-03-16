<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

/**
 * @deprecated
 */
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
