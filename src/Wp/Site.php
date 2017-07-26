<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use Exception;

/**
 * Interface Site
 *
 * @package WpProvision\Wp
 */
interface Site {

	/**
	 * @param string $url The site url (e.g. example.dev/en/)
	 * @param int $network_id
	 *
	 * @return bool
	 */
	public function exists( $url, $network_id = 0 );

	/**
	 * @param string $url The site url (e.g. example.dev/en/)
	 * @param int $network_id
	 *
	 * @return int
	 */
	public function siteId( $url, $network_id = 0 );

	/**
	 * @param string $url (Site URL including protocol, e.g. https://whatever.mysite.tld/en/ )
	 * @param array $options
	 *      string $options[ 'user_email' ]
	 *      string $options[ 'title' ]
	 *      bool $options[ 'private' ]
	 *      string $options[ 'slug' ] (Ignores the URL parameter and just create the site with this slug)
	 * @param int $network_id
	 * @param bool $graceful Deprecated Set to false to throw exceptions if anything goes wrong
	 *
	 * @throws Exception
	 * @return int
	 */
	public function create( $url, array $options = [ ], $network_id = 0, $graceful = true );

	/**
	 * @param array $options
	 *      int $options[ 'network_id' ]
	 *      array $options[ 'filter' ] (Associative array: field => value)
	 *      int[] $options[ 'site_in' ]
	 *      string $options[ 'fields' ] (Fields to return. If only one field is provided, the returned array is flat list)
	 * @return array (list of associative arrays with [ $field => $value ] structure, unless a single $fields option is provided)
	 */
	public function list( array $options = [] ) : array;
}
