<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\Command;
use WpProvision\Exception\Wp\InvalidArgumentException;

/**
 * Class WpCliDb
 *
 * @package WpProvision\Wp
 */
final class WpCliDb implements Db {

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
	 * @return array
	 */
	public function check() {
		// TODO: Implement check() method.
	}

	/**
	 * @return bool
	 */
	public function create() {
		// TODO: Implement create() method.
	}

	/**
	 * @return bool
	 */
	public function drop() {
		// TODO: Implement drop() method.
	}

	/**
	 * @param string $file (Full path of the export file)
	 * @param string[] $tables (List of tables to export)
	 * @param string[]|array[] $sql_arguments (Associative arguments key → value to pass to mysql)
	 *
	 * @return bool
	 */
	public function export( $file, array $tables = [], array $sql_arguments = [] ) {
		// TODO: Implement export() method.
	}

	/**
	 * @param string $file (Full path of the import file)
	 *
	 * @return bool
	 */
	public function import( $file ) {

		// Todo: Refactor to something testable
		$file = realpath( $file );
		if ( false === $file ) {
			throw new InvalidArgumentException( "Import file not found" );
		}

		$arguments = [ 'import', $file ];
		try {
			$this->wp_cli->run( $arguments );
			return true;
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * @return bool
	 */
	public function optimize() {
		// TODO: Implement optimize() method.
	}

	/**
	 * @param string $query
	 * @param string[]|array[] $sql_arguments (Associative arguments key → value to pass to mysql)
	 *
	 * @return string
	 */
	public function query( $query, array $sql_arguments = [] ) {
		// TODO: Implement query() method.
	}

	/**
	 * @return bool
	 */
	public function repair() {
		// TODO: Implement repair() method.
	}

	/**
	 * @return bool
	 */
	public function reset() {
		// TODO: Implement reset() method.
	}

	/**
	 * @param string[] $tables (List of table patterns)
	 * @param array $options (Associative options key → value. Possible keys: 'scope' (string), 'network' (bool)
	 * 'all_tables_with_prefix' (bool), 'all_tables' (bool))
	 *
	 * @return array
	 */
	public function tables( array $tables = [], array $options = [] ) {
		// TODO: Implement tables() method.
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	private function arguments( array $args = [] ) {

		return array_merge( [ 'db' ], $args );
	}
}