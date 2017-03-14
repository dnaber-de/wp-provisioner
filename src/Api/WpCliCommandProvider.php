<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use WpProvision\Command\WpCliCommand;
use WpProvision\Utils\Sha1PasswordGenerator;
use WpProvision\Wp\Core;
use WpProvision\Wp\Plugin;
use WpProvision\Wp\Site;
use WpProvision\Wp\User;
use WpProvision\Wp\WpCliCore;
use WpProvision\Wp\WpCliPlugin;
use WpProvision\Wp\WpCliSite;
use WpProvision\Wp\WpCliUser;

/**
 * Class WpCliCommandProvider
 *
 * Temporary solution to quickly instantiate Wp-Command instances. It's in fact a
 * courier anti-pattern and will be replaced by a IOC container
 *
 * @package WpProvision\Api
 */
final class WpCliCommandProvider implements WpCommandProvider {

	/**
	 * @var WpCliCommand
	 */
	private $wp_cli;

	private $core;
	private $plugin;
	private $site;
	private $user;

	public function __construct( WpCliCommand $wp_cli ) {

		$this->wp_cli = $wp_cli;
		$this->core = new WpCliCore( $wp_cli, new Sha1PasswordGenerator() );
		$this->plugin = new WpCliPlugin( $wp_cli );
		$this->user = new WpCliUser( $wp_cli );
		$this->site = new WpCliSite( $wp_cli, $this->user, $this->plugin );
	}

	/**
	 * @return Core
	 */
	public function core() {

		return $this->core;
	}

	/**
	 * @return Plugin
	 */
	public function plugin() {

		return $this->plugin;
	}

	/**
	 * @return Site
	 */
	public function site() {

		return $this->site;
	}

	/**
	 * @return User
	 */
	public function user() {

		return $this->user;
	}

}
