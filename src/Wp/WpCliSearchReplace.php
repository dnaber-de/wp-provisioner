<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\Command;

/**
 * Class WpCliSearchReplace
 *
 * @package WpProvision\Wp
 */
final class WpCliSearchReplace implements SearchReplace {

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
	 * @param $pattern
	 * @param $replacement
	 * @param array $options Keys: (array) tables
	 *                             (bool) dry_run,
	 *                             (bool) network,
	 *                             (bool) all_tables_with_prefix,
	 *                             (bool) all_tables,
	 *                             (array) skip_columns,
	 *                             (array) include_columns,
	 *                             (bool) precise
	 *                             (bool) recurse_objects
	 *                             (bool) regex
	 *                             (string) regex_flags
	 *
	 * @return bool
	 */
	public function searchReaplace( $pattern, $replacement, array $options = [] ) {

		$arguments = [ 'search-replace', $pattern, $replacement ];
		if ( isset( $options[ 'tables' ] ) && $options[ 'tables' ] ) {
			$arguments = array_merge( $arguments, $options[ 'tables' ] );
		}

		$arguments = array_merge( $arguments, $this->buildOptions( $options ) );
		try {
			return $this->wp_cli->run( $arguments );

			return true;
		} catch ( \Throwable $e ) {
			// Todo: Wrap any possible Exception with a WpProvison\Exception
			throw $e;
		}
	}

	private function buildOptions( array $options ) {

		$arguments = [];

		$flags = [
			'dry_run' => '--dry-run',
			'network' => '--network',
			'all_tables' => '--all-tables',
			'all_tables_with_prefix' => '--all-tables-with-prefix',
			'precise' => '--precise',
			'recurse_objects' => '--recurse-objects',
			'regex' => '--regex'
		];
		foreach ( $flags as $flag => $argument ) {
			if ( empty( $options[ $flag ] ) ) {
				continue;
			}
			true === $options[ $flag ] and $arguments[] = $argument;
		}

		if ( isset( $options[ 'skip_columns' ] ) and is_array( $options[ 'skip_columns' ] ) ) {
			$arguments[] = '--skip-columns=' . implode(',',  $options[ 'skip_columns' ]  );
		}
		if ( isset( $options[ 'include_columns' ] ) and is_array( $options[ 'include_columns' ] ) ) {
			$arguments[] = '--include-columns=' . implode(',',  $options[ 'include_columns' ]  );
		}

		if ( isset( $options[ 'regex_flags' ] ) and is_string( $options[ 'regex_flags' ] ) ) {
			$arguments[] = "--regex-flags={$options['regex_flags']}";
		}

		return $arguments;
	}
}