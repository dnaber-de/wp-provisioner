<?php # -*- coding: utf-8 -*-

namespace WpProvision\Utils;

/**
 * Class CliOutputParser
 *
 * Todo: Refactor to something useful (injectable)
 *
 * @package WpProvision\Utils
 */
trait CliOutputParser {

	/**
	 * @param $output
	 * @param string $line_break (Optional, default to \n)
	 *
	 * @return string[]
	 */
	private function parseList( $output, $line_break = "\n" ) {

		$list = explode( $line_break, trim( (string) $output ) );

		return $this->trimList( $list );
	}

	/**
	 * @param array $list
	 *
	 * @return string[]
	 */
	private function trimList( array $list ) {

		array_walk( $list, function( &$el ) {

			$el = trim( $el );
			$el or $el = false;
		} );

		return array_filter( $list );
	}
}