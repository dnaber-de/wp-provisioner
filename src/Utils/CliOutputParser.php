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
	 * @param string $line_separator (Optional, default to \n)
	 *
	 * @return string[]
	 */
	private function parseList( $output, $line_separator = "\n" ) {

		$list = explode( $line_separator, trim( (string) $output ) );

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

	private function parseTable( string $output, $line_separator = "\n", $column_separator = "\t" ) : array {

		$lines = $this->parseList( $output, $line_separator );
		if ( empty( $lines ) ) {
			return $lines;
		}
		$columize = function( string $row ) use ( $column_separator ) {
			$columns = explode( $column_separator, $row );
			return array_map( 'trim', $columns );
		};
		$headers = $columize( array_shift( $lines ) );

		$table = [];
		array_walk( $lines, function( $row ) use ( $headers, &$table, $columize ) {
			$columns = $columize( $row );
			$table[] = array_combine( $headers, $columns );
		} );

		return $table;
	}
}