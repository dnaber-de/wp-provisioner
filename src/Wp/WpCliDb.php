<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\Command;
use WpProvision\Exception\Wp\InvalidArgumentException;
use WpProvision\Utils\CliOutputParser;

/**
 * Class WpCliDb
 *
 * @package WpProvision\Wp
 */
final class WpCliDb implements Db {

	use CliOutputParser;

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

		$arguments = $this->concatArguments( [ 'import', $file ] );
		try {
			$this->wp_cli->run( $arguments );
			return true;
		} catch ( \Throwable $e ) {

			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
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
	 * @throws Todo
	 *
	 * @return array
	 */
	public function query( $query, array $sql_arguments = [] ) {

		$arguments = $this->concatArguments(
            ['query', escapeshellarg((string)$query)],
			$sql_arguments
			// Todo: handle $sql_arguments
		);
		try {
			$result = $this->wp_cli->run( $arguments );

			return $this->parseList( $result );
		} catch ( \Throwable $e ) {
			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
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

		try {
			$arguments = $this->concatArguments(
				[ 'tables' ],
				$tables,
                $this->buildOptions( $options )
			);
			$result = $this->wp_cli->run( $arguments );
			return $this->parseList( $result );

		} catch( \Throwable $e ) {
			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
	}

	/**
	 * @param string[][] ...$arguments
	 *
	 * @return array
	 */
	private function concatArguments( array ...$arguments ) {

		return array_merge( [ 'db' ], ...$arguments );
	}

    private function buildOptions( array $options ) {

        $arguments = [];

        $flags = [
            'scope' => '--scope',
            'network' => '--network',
            'all_tables' => '--all-tables',
            'all_tables_with_prefix' => '--all-tables-with-prefix',
        ];
        foreach ( $flags as $flag => $argument ) {
            if ( empty( $options[ $flag ] ) ) {
                continue;
            }
            true === $options[ $flag ] and $arguments[] = $argument;
        }

        return $arguments;
    }
}