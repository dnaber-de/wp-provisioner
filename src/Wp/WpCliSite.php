<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use
	WpProvision\Command,
	Exception,
	InvalidArgumentException,
	LogicException;

/**
 * Class WpCliSite
 *
 * @package WpProvision\Wp
 */
class WpCliSite implements Site {

	/**
	 * @var Command\WpCliCommand
	 */
	private $wp_cli;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * @param Command\WpCliCommand $wp_cli
	 * @param User                 $user
	 * @param Plugin               $plugin
	 */
	public function __construct( Command\WpCliCommand $wp_cli, User $user, Plugin $plugin ) {

		$this->wp_cli = $wp_cli;
		$this->user   = $user;
		$this->plugin = $plugin;
	}

	/**
	 * @param string $url The site url (e.g. example.dev/en/)
	 * @param int $network_id
	 *
	 * @return bool
	 */
	public function exists( $url, $network_id = 0 ) {

		return 0 !== $this->siteId( $url, (int) $network_id );
	}

	/**
	 * @link http://wp-cli.org/commands/site/list/
	 *
	 * @param string $url The site url (e.g. example.dev/en/)
	 * @param int $network_id
	 *
	 * @return int
	 */
	public function siteId( $url, $network_id = 0 ) {

		$arguments = [ 'site', 'list', '--fields=blog_id,url' ];
		if ( $network_id ) {
			$arguments[] = '--network=' . (int) $network_id;
		}

		/**
		 * @param $url_1
		 * @param $url_2
		 *
		 * @return bool
		 */
		$urls_match = function( $url_1, $url_2 ) {

			if ( parse_url( $url_1, PHP_URL_HOST ) !== parse_url( $url_2, PHP_URL_HOST ) ) {
				return FALSE;
			}

			$url_1_path = parse_url( $url_1, PHP_URL_PATH );
			$url_2_path = parse_url( $url_2, PHP_URL_PATH );
			// make sure that 'myhost.dev' matches 'myhost.dev/'
			$url_1_path = rtrim( $url_1_path, '/' ) . '/';
			$url_2_path = rtrim( $url_2_path, '/' ) . '/';

			if ( $url_1_path !== $url_2_path ) {
				return FALSE;
			}

			return TRUE;
		};

		/**
		 * Parse a line from WP-CLI output
		 * e.g. "2   siteurl.tld"
		 * into an array with 'id' and 'url'
		 *
		 * @param $line
		 *
		 * @return NULL|array
		 */
		$parse_site = function( $line ) {
			$line = trim( $line );
			list( $id, $url ) = preg_split( '~\s+~', $line );

			if ( ! is_numeric( $id ) ) {
				return NULL; // skip table header
			}

			return [ 'id' => (int) $id, 'url' => trim( $url ) ];
		};

		try {
			$result = trim( $this->wp_cli->run( $arguments ) );
			$sites  = explode( "\n",$result );
			$sites  = array_map( $parse_site, $sites );
			$sites  = array_filter( $sites, 'is_array' );
			foreach ( $sites as $site ) {
				if ( $urls_match( $site[ 'url' ], $url ) ) {
					return $site[ 'id' ];
				}
			}

			return 0;
		} catch ( Exception $e ) {
			/**
			 * Todo: Should the exception better be catched inside the WpCli object?
			 * The exception is the expected behaviour if the site does not exists,
			 * therefore we catch it and do not propagate it up
			 */
			return 0;
		}
	}

	/**
	 * @link http://wp-cli.org/commands/site/create/
	 *
	 * @param string $url (Site URL including protocol, e.g. https://whatever.mysite.tld/en/ )
	 * @param array $attributes
	 *      string $attributes[ 'user_email' ]
	 *      string $attributes[ 'title' ]
	 *      bool $attributes[ 'private' ]
	 *      string $attributes[ 'slug' ] (Ignores the URL parameter and just create the site with this slug)
	 * @param int $network_id
	 * @param bool $graceful Set to FALSE to throw exceptions if anything goes wrong
	 *
	 * @throws Exception
	 * @return int
	 */
	public function create( $url, array $attributes = [ ], $network_id = 0, $graceful = TRUE ) {

		$user_email = isset( $attributes[ 'user_email' ] )
			? $attributes[ 'user_email' ]
			: NULL;
		if ( $user_email ) {
			$user_exists = $this->user->exists( $user_email );
			if ( ! $user_exists && ! $graceful ) {
				throw new InvalidArgumentException( "User {$user_email} does not exist" );
			} elseif ( ! $user_exists ) {
				$user_email = NULL;
			}
		}

		$site_id = $this->siteId( $url );
		if ( 0 !== $site_id && ! $graceful ) {
			throw new InvalidArgumentException( "Site {$url} already exists" );
		} elseif ( 0 !== $site_id ) {

			return $site_id;
		}

		// temporary slug
		$slug    = substr( sha1( $url ), -8 );
		$use_url = TRUE;
		if ( isset( $attributes[ 'slug' ] ) ) {
			$use_url = FALSE;
			$slug    = $attributes[ 'slug' ];
		}

		// check dependencies
		$cli_site_url_plugin = 'wp-cli-site-url';
		if ( $use_url && ! $this->plugin->isActive( $cli_site_url_plugin, [ 'network' => TRUE ] ) ) {
			// try to activate it, if installed (it should, it is a dependency of WP Provisioner)
			if ( $this->plugin->isInstalled( $cli_site_url_plugin ) ) {
				$this->plugin->activate( $cli_site_url_plugin, [ 'network' => TRUE ] );
			}
			// check again...
			if ( ! $this->plugin->isActive( $cli_site_url_plugin, [ 'network' => TRUE ] ) ) {
				throw new LogicException( "Plugin inpsyde/{$cli_site_url_plugin} is not available but required" );
			}
		}

		$create_args = [ 'site', 'create', "--slug={$slug}", '--porcelain' ];

		if ( $user_email ) {
			$create_args[] = "--email={$user_email}";
		}
		if ( ! empty( $attributes[ 'title' ] ) ) {
			$create_args[] = "--title={$attributes[ 'title' ]}";
		}
		if ( $network_id ) {
			$network_id = (int) $network_id;
			$create_args[] = "--network_id={$network_id}";
		}
		if ( ! empty( $attributes[ 'private' ] ) ) {
			$create_args[] = '--private';
		}

		$result  = trim( $this->wp_cli->run( $create_args ) );
		$site_id = (int) $result;

		if ( ! $use_url ) {
			return $site_id;
		}

		$this->wp_cli->run(
			[ 'site-url', 'update', $site_id, $url ]
		);

		return $site_id;
	}
}
