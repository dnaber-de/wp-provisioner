<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

/**
 * Interface Plugin
 *
 * @package WpProvision\Wp
 */
interface Plugin {

	/**
	 * @param string|array $plugin The plugin slug or a list of slugs (e.g. 'multilingual-press', 'akismet' )
	 * @param array        $options
	 *      bool   $options[ 'network' ] If set to TRUE, the plugin gets activated networkwide, default: false
	 *      bool   $options[ 'all' ] If set to TRUE, all plugins gets activated (regardless of $plugin parameter)
	 *      string $option[ 'site_url' ] The site_url the plugin should be activated in, default: network main site
	 *
	 * @return bool
	 */
	public function activate( $plugin, array $options = [ ] );

	/**
	 * @param string|array $plugin The plugin slug or a list of slugs (e.g. 'multilingual-press', 'akismet' )
	 * @param array        $options
	 *      bool   $options[ 'network' ] If set to TRUE, the plugin gets activated network wide, default: false
	 *      bool   $options[ 'all' ] If set to TRUE, all plugins gets activated (regardless of $plugin parameter), default: false
	 *      bool   $options[ 'uninstall' ] If set to TRUE, the plugin gets uninstalled after deactivation, default: false
	 *      string $option[ 'site_url' ] The site_url the plugin should be deactivated, default: network main site
	 *
	 * @return bool
	 */
	public function deactivate( $plugin, array $options = [ ] );

	/**
	 * Is installed means that the plugin is available for activation
	 *
	 * @param string $plugin  The plugin slug (e.g. 'multilingual-press', 'akismet' )
	 * @param array  $options (No options supported at the moment)
	 *
	 * @return bool
	 */
	public function isInstalled( $plugin, array $options = [ ] );

	/**
	 * @param string $plugin The plugin slug (e.g. 'multilingual-press', 'akismet' )
	 * @param array  $options
	 *      bool   $options[ 'network' ]  Check if the plugin is activated network wide, default: false
	 *      string $options[ 'site_url' ] The URL of the site to check (default: the network main site, unused in single-site installs)
	 *
	 * @return bool
	 */
	public function isActive( $plugin, array $options = [ ] );

	/**
	 * @param string|array $plugin The plugin slug or a list of slugs (e.g. 'multilingual-press', 'akismet' )
	 * @param array        $options
	 *      bool   $options[ 'deactivate' ] Deactivate plugin before uninstallation, default: TRUE
	 *      bool   $option[ 'delete' ] Deletes files after uninstallation , default: false
	 *      string $option[ 'site_url' ] The site_url the uninstall routines should run in, default: network main site
	 *
	 * @return bool
	 */
	public function uninstall( $plugin, array $options = [ ] );
}
