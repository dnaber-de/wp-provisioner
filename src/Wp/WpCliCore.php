<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\Command;
use WpProvision\Utils\PasswordGenerator;
use InvalidArgumentException;
use Exception;

/**
 * Class WpCliCore
 *
 * @package WpProvision\Wp
 */
final class WpCliCore implements Core {

	/**
	 * @var Command
	 */
	private $wp_cli;

	/**
	 * @var PasswordGenerator
	 */
	private $pw_generator;

	/**
	 * @param Command $wp_cli
	 * @param PasswordGenerator $pw_generator
	 */
	public function __construct( Command $wp_cli, PasswordGenerator $pw_generator ) {

		$this->wp_cli       = $wp_cli;
		$this->pw_generator = $pw_generator;
	}

	/**
	 * @link http://wp-cli.org/commands/core/is-installed/
	 *
	 * @param bool $network If multisite is installed
	 *
	 * @return bool
	 */
	public function isInstalled( $network = false ) {

		$arguments = [ 'core', 'is-installed' ];
		if ( $network ) {
			$arguments[] = '--network';
		}

		try {
			$this->wp_cli->run( $arguments );
			return TRUE;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * @link http://wp-cli.org/commands/core/install/
	 *
	 * @param string $url    URL of the new site
	 * @param array $admin
	 *                       string $admin[ 'email' ] (required)
	 *                       string $admin[ 'login' ] (required)
	 *                       string $admin[ 'password' ] (optional, will be generated if not provided)
	 * @param array $options
	 *                       string $options[ 'title' ]
	 *                       bool   $options[ 'skip_email' ] Skip the information email, default: false
	 * @param bool $graceful Throw exceptions, when set to false, default: TRUE
	 *
	 * @return bool
	 */
	public function install( $url, array $admin, array $options = [ ], $graceful = TRUE ) {

		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			throw new InvalidArgumentException( "'{$url}' is not a valid URL" );
		}

		foreach ( [ 'email', 'login' ] as $admin_key ) {
			if ( empty( $admin[ $admin_key ] ) ) {
				throw new InvalidArgumentException( "Missing array key '{$admin_key}' in \$admin parameter" );
			}
		}

		if ( ! filter_var( $admin[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
			throw new InvalidArgumentException( "{$admin[ 'email' ]} is not a valid email" );
		}

		$password = isset( $admin[ 'password' ] )
			? $admin[ 'password' ]
			: $this->pw_generator->generatePassword();
		$title    = isset( $options[ 'title' ] )
			? $options[ 'title' ]
			: "Site installed by WP Provisioner";

		$arguments = [
			'core',
			'install',
			"--url={$url}",
			"--title={$title}",
			"--admin_user={$admin[ 'login' ]}",
			"--admin_email={$admin[ 'email' ]}",
			"--admin_password={$password}"
		];
		if ( isset( $options[ 'skip_email' ] ) && $options[ 'skip_email' ] ) {
			$arguments[] = '--skip-email';
		}

		$this->wp_cli->run( $arguments );

		return TRUE;
	}

	/**
	 * @link http://wp-cli.org/commands/core/multisite-convert/
	 *
	 * @param array $options
	 *      string $options[ 'base_path' ] Base URL path for all sites, default: '/'
	 *      string $options[ 'title' ] Title of the network
	 *      bool   $options[ 'subdomains' ] Subdomain install? Default: TRUE
	 *
	 * @return bool
	 */
	public function multisiteConvert( array $options = [ ] ) {

		$base_path = isset( $options[ 'base_path' ] )
			? '/' . ltrim( $options[ 'base_path' ], '/' )
			: '/';

		$subdomains = isset( $options[ 'subdomains' ] )
			? (bool) $options[ 'subdomains' ]
			: TRUE;


		$arguments = [ 'core', 'multisite-convert', "--base={$base_path}" ];
		if ( $subdomains ) {
			$arguments[] = '--subdomains';
		}
		if ( isset( $options[ 'title' ] ) ) {
			$arguments[] = "--title={$options[ 'title' ]}";
		}

		echo $this->wp_cli->run( $arguments );

		return TRUE;
	}

	/**
	 * @param string $url      The URL of the network (e.g. http://example.dev/)
	 * @param array $admin
	 *                         string $admin[ 'email' ] (required)
	 *                         string $admin[ 'login' ] (required)
	 *                         string $admin[ 'password' ] (optional, will be generated if not provided)
	 * @param array $options
	 *                         string $options[ 'title' ]
	 *                         bool   $options[ 'skip_email' ] Skip the information email, default: false
	 *                         bool   $options[ 'subdomains' ] Subdomain install, default: TRUE
	 * @param bool $graceful   Throw exceptions, when set to false, default: TRUE
	 *
	 * @return bool
	 */
	public function multisiteInstall( $url, array $admin, array $options = [ ], $graceful = TRUE ) {

		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			throw new InvalidArgumentException( "'{$url}' is not a valid URL" );
		}

		foreach ( [ 'email', 'login' ] as $admin_key ) {
			if ( empty( $admin[ $admin_key ] ) ) {
				throw new InvalidArgumentException( "Missing array key '{$admin_key}' in \$admin parameter" );
			}
		}

		if ( ! filter_var( $admin[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
			throw new InvalidArgumentException( "{$admin[ 'email' ]} is not a valid email" );
		}

		$url_components = parse_url( $url );
		$scheme         = isset( $url_components[ 'scheme' ] )
			? "{$url_components[ 'scheme' ]}://"
			: 'http://';
		if ( ! isset( $url_components[ 'host' ] ) ) {
			throw new InvalidArgumentException( "{$url} should contain a host" );
		}
		// URL user and password are not supported
		$host = $url_components[ 'host' ];
		$port = isset( $url_components[ 'port' ] )
			? ":{$url_components[ 'port' ]}"
			: '';

		$network_url = $scheme . $host . $port;
		$network_path = isset( $url_components[ 'path' ] )
			? $url_components[ 'path' ]
			: '/';

		$password = isset( $admin[ 'password' ] )
			? $admin[ 'password' ]
			: $this->pw_generator->generatePassword();
		$title    = isset( $options[ 'title' ] )
			? $options[ 'title' ]
			: "Multisite installed by WP Provisioner";

		$arguments = [
			'core',
			'multisite-install',
			"--url={$network_url}",
			"--base={$network_path}",
			"--title={$title}",
			"--admin_user={$admin[ 'login' ]}",
			"--admin_email={$admin[ 'email' ]}",
			"--admin_password={$password}"
		];

		if ( ! isset( $options[ 'subdomains'] ) || false !== $options[ 'subdomains' ] ) {
			$arguments[] = '--subdomains';
		}
		if ( isset( $options[ 'skip_email' ] ) && $options[ 'skip_email' ] ) {
			$arguments[] = '--skip-email';
		}

		$this->wp_cli->run( $arguments );

		return TRUE;
	}
}
