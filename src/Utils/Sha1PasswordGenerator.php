<?php # -*- coding: utf-8 -*-

namespace WpProvision\Utils;

/**
 * Class Sha1PasswordGenerator
 *
 * @package WpProvision\Utils
 */
final class Sha1PasswordGenerator implements PasswordGenerator {

	/**
	 * @var int
	 */
	private $length = 20;

	/**
	 * @param int $length Values between 1 and 40 are valid
	 */
	public function __construct( $length = 20 ) {

		$length = (int) $length;
		if ( 1 > $length )
			$length = 20;
		if ( 40 < $length )
			$length = 40;

		$this->length = $length;
	}

	/**
	 * @return string
	 */
	public function generatePassword() {

		$random = microtime() / mt_rand( 1, time() );

		return substr( sha1( $random ), 0, $this->length );
	}

}
