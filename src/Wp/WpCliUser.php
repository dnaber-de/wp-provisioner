<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\Command;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

/**
 * Class WpCliUser
 *
 * @package WpProvision\Wp
 */
final class WpCliUser implements User {

	/**
	 * @var Command
	 */
	private $wp_cli;

	/**
	 * @param Command $wp_cli
	 */
	public function __construct( Command $wp_cli ) {

		$this->wp_cli = $wp_cli;
	}

	/**
	 * @param string $email_or_login
	 *
	 * @return int
	 */
	public function userId( $email_or_login ) {

		$arguments = [ 'user', 'get', $email_or_login, '--field=ID' ];

		try {
			$result = trim( $this->wp_cli->run( $arguments ) );

			return (int) $result;
		} catch ( \Throwable $e ) {
			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
	}

	/**
	 * @param bool $email_or_login
	 *
	 * @return bool
	 */
	public function exists( $email_or_login ) {

		return 0 !== $this->userId( $email_or_login );
	}

	/**
	 * @link http://wp-cli.org/commands/user/create/
	 *
	 * @param $login
	 * @param $email
	 * @param array $options
	 *      string $options[ 'role' ]
	 *      string $options[ 'password' ]
	 *      string $options[ 'first_name' ]
	 *      string $options[ 'last_name' ]
	 *      string $options[ 'display_name' ]
	 *      bool $options[ 'send_mail' ]
	 *      DateTimeInterface $options[ 'registered_at' ]
	 * @param string $site_url
	 * @param bool $graceful Set to false to throw exceptions when something goes wrong (e.g. the user already exists)
	 *
	 * @throws Exception
	 * @return int
	 */
	public function create( $login, $email, array $options = [], $site_url = '', $graceful = true ) {

		$login_exists = $this->exists( $login );
		if ( $login_exists && ! $graceful ) {
			$user_id = $this->userId( $login );
			// Todo
			throw new InvalidArgumentException( "User {$login} already exists (ID: {$user_id})" );
		} elseif ( $login_exists ) {

			return $this->userId( $login );
		}

		$email_exists = $this->exists( $email );
		if ( $email_exists && ! $graceful ) {
			$user_id = $this->userId( $login );
			// Todo
			throw new InvalidArgumentException( "User {$email} already exists (ID: {$user_id})" );
		} elseif( $email_exists ) {

			return $this->userId( $email );
		}

		$process_arguments = [ 'user', 'create', $login, $email, '--porcelain' ];
		$attr_keys = [
			'role'         => '--role=',
			'password'     => '--user_pass=',
			'first_name'   => '--first_name=',
			'last_name'    => '--last_name=',
			'display_name' => '--display_name='
		];
		foreach ( $attr_keys as $attribute_key => $process_attribute ) {
			if ( empty( $options[ $attribute_key ] ) ) {
				continue;
			}
			$process_arguments[] = $process_attribute . $options[ $attribute_key ];
		}

		if ( isset( $options[ 'send_mail' ] ) && $options[ 'send_mail' ] ) {
			$process_arguments[] = '--send_mail';
		}

		if ( isset( $options[ 'registered_at' ] ) && is_a( $options[ 'registered_at' ], 'DateTimeInterface' ) ) {
			/* @var DateTimeInterface $options[ 'registered_at' ] */
			$process_arguments[] = '--user_registered=' . $options[ 'registered_at' ]->format( 'Y-m-d' );
		}

		try {
			$result = trim( $this->wp_cli->run( $process_arguments ) );

			return (int) $result;
		} catch ( \Throwable $e ) {
			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
	}
}
