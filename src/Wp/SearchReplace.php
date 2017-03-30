<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

/**
 * Interface SearchReplace
 *
 * @package WpProvision\Wp
 */
interface SearchReplace {

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
	public function searchReaplace( $pattern, $replacement, array $options = [] );
}