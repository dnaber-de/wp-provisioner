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
	 * @deprecated Can only be set via ENV or console option
	 * @param $wp_dir
	 */
	public function setWpDir( $wp_dir );
}
