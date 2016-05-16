<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

/**
 * Interface Core
 *
 * @package WpProvision\Wp
 */
interface Core {

	/**
	 * @param bool $network If multisite is installed
	 *
	 * @return bool
	 */
	public function isInstalled( $network = FALSE );

	/**
	 * @param string $url    URL of the new site
	 * @param array $admin
	 *                       string $admin[ 'email' ] (required)
	 *                       string $admin[ 'login' ] (required)
	 *                       string $admin[ 'password' ] (optional, will be generated if not provided)
	 * @param array $options
	 *                       string $options[ 'title' ]
	 *                       bool   $options[ 'skip_email' ] Skip the information email, default: FALSE
	 * @param bool $graceful Throw exceptions, when set to FALSE, default: TRUE
	 *
	 * @return bool
	 */
	public function install( $url, array $admin, array $options = [ ], $graceful = TRUE );

	/**
	 * @param array $options
	 *      string $options[ 'base_path' ] Base URL path for all sites, default: '/'
	 *      string $options[ 'title' ] Title of the network
	 *      bool   $options[ 'subdomains' ] Subdomain install? Default: TRUE
	 *
	 * @return bool
	 */
	public function multisiteConvert( array $options = [ ] );

	/**
	 * @param string $url    The URL of the network (e.g. http://example.dev/)
	 * @param array $admin
	 *                       string $admin[ 'email' ] (required)
	 *                       string $admin[ 'login' ] (required)
	 *                       string $admin[ 'password' ] (optional, will be generated if not provided)
	 * @param array $options
	 *                       string $options[ 'title' ]
	 *                       bool   $options[ 'skip_email' ] Skip the information email, default: FALSE
	 *                       bool   $options[ 'subdomains' ] Subdomain install, default: TRUE
	 * @param bool $graceful Throw exceptions, when set to FALSE, default: TRUE
	 *
	 * @return bool
	 */
	public function multisite_install( $url, array $admin, array $options = [ ], $graceful = TRUE );
}
