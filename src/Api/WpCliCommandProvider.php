<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use Psr\Container\ContainerInterface;
use WpProvision\Wp\Cli;
use WpProvision\Wp\Core;
use WpProvision\Wp\Db;
use WpProvision\Wp\Plugin;
use WpProvision\Wp\SearchReplace;
use WpProvision\Wp\Site;
use WpProvision\Wp\User;
use WpProvision\Wp\WpCliCli;
use WpProvision\Wp\WpCliCore;
use WpProvision\Wp\WpCliDb;
use WpProvision\Wp\WpCliPlugin;
use WpProvision\Wp\WpCliSearchReplace;
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
	 * @var WpCliCore
	 */
	private $container;

	private $cli;
	private $core;
	private $db;
	private $plugin;
	private $site;
	private $user;
	private $search_replace;

	public function __construct( ContainerInterface $container ) {

		$this->container = $container;
	}

	/**
	 * @return Cli
	 */
	public function cli() {

		$this->cli or $this->cli = $this->container->get( WpCliCli::class );

		return $this->cli;
	}

	/**
	 * @return Core
	 */
	public function core() {

		$this->core or $this->core = $this->container->get( WpCliCore::class );

		return $this->core;
	}

	/**
	 * @return Db
	 */
	public function db() {

		$this->db or $this->db = $this->container->get( WpCliDb::class );

		return $this->db;
	}

	/**
	 * @return Plugin
	 */
	public function plugin() {

		$this->plugin or $this->plugin = $this->container->get( WpCliPlugin::class );

		return $this->plugin;
	}

	/**
	 * @return SearchReplace
	 */
	public function searchReplace() {

		$this->search_replace or $this->search_replace = $this->container->get( WpCliSearchReplace::class );

		return $this->search_replace;
	}

	/**
	 * @return Site
	 */
	public function site() {

		$this->site or $this->site = $this->container->get( WpCliSite::class );

		return $this->site;
	}

	/**
	 * @return User
	 */
	public function user() {

		$this->user or $this->user = $this->container->get( WpCliUser::class );

		return $this->user;
	}

}
