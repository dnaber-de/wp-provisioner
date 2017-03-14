<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

/**
 * Interface Db
 *
 * @package WpProvision\Wp
 */
interface Db {

	/**
	 * @return bool
	 */
	public function check();

	/**
	 * @return bool
	 */
	public function create();

	/**
	 * @return bool
	 */
	public function drop();

	/**
	 * @param string $file (Full path of the export file)
	 * @param string[] $tables (List of tables to export)
	 * @param string[]|array[] $sql_arguments (Associative arguments key → value to pass to mysql)
	 *
	 * @return bool
	 */
	public function export( $file, array $tables = [], array $sql_arguments = [] );

	/**
	 * @param string $file (Full path of the import file)
	 *
	 * @return bool
	 */
	public function import( $file );

	/**
	 * @return bool
	 */
	public function optimize();

	/**
	 * @param string $query
	 * @param string[]|array[] $sql_arguments (Associative arguments key → value to pass to mysql)
	 *
	 * @return string
	 */
	public function query( $query, array $sql_arguments = [] );

	/**
	 * @return bool
	 */
	public function repair();

	/**
	 * @return bool
	 */
	public function reset();

	/**
	 * @param string[] $tables (List of table patterns)
	 * @param array $options (Associative options key → value. Possible keys: 'scope' (string), 'network' (bool)
	 * 'all_tables_with_prefix' (bool), 'all_tables' (bool))
	 *
	 * @return array
	 */
	public function tables( array $tables = [], array $options = [] );
}