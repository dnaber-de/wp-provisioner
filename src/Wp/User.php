<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use Exception;

interface User {

	/**
	 * @param string $email_or_login
	 *
	 * @return int
	 */
	public function userId( $email_or_login );

	/**
	 * @param bool $email_or_login
	 *
	 * @return bool
	 */
	public function exists( $email_or_login );

	/**
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
	 * @param bool $graceful Deprecated! Set to false to throw exceptions when something goes wrong (e.g. the user already exists)
	 *
	 * @throws Exception
	 * @return int
	 */
	public function create( $login, $email, array $options = [ ], $site_url = '', $graceful = true );
}
