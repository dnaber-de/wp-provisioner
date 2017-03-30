<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use MonkeryTestCase\MockeryTestCase;
use Mockery;
use WpProvision\Command\Command;

/**
 * Class WpCliSearchReplaceTest
 *
 * @package WpProvision\Wp
 */
class WpCliSearchReplaceTest extends MockeryTestCase  {

	/**
	 * @see WpCliSearchReplace::searchReaplace()
	 */
	public function testSearchReplace() {

		$pattern = 'http://public.tld';
		$replacement = 'http://local.dev';
		$expected_arguments = [ 'search-replace', $pattern, $replacement ];

		$command = Mockery::mock( Command::class );
		$command->shouldReceive( 'run' )
			->once()
			->with( Mockery::on( function( $arguments ) use ( $expected_arguments ) {
				$this->assertSame(
					$expected_arguments,
					$arguments
				);
				return true;
			} ) );

		$testee = new WpCliSearchReplace( $command );
		$this->assertTrue(
			$testee->searchReaplace( $pattern, $replacement )
		);
	}

	/**
	 * @dataProvider searchReplaceWithOptionsTestData
	 * @see WpCliSearchReplace::searchReaplace()
	 *
	 * @param array $parameter
	 * @param array $expected_arguments
	 */
	public function testSearchReplaceWithOptions( array $parameter, array $expected_arguments ) {

		$command = Mockery::mock( Command::class );
		$command->shouldReceive( 'run' )
			->once()
			->with( Mockery::on( function( $arguments ) use ( $expected_arguments ) {
				$this->assertSame(
					$expected_arguments,
					$arguments
				);
				return true;
			} ) );

		$testee = new WpCliSearchReplace( $command );
		$this->assertTrue(
			$testee->searchReaplace( $parameter[ 'pattern' ], $parameter[ 'replacement' ], $parameter[ 'options' ] )
		);
	}

	/**
	 * @see testSearchReplaceWithOptions()
	 */
	public function searchReplaceWithOptionsTestData() {

		$data = [];

		$data[ 'with_tables' ] = [
			[
				'pattern' => 'http://public.tld',
				'replacement' => 'http://local.dev',
				'options' => [
					'tables' => [ 'wp_*_posts', 'wp_*_comments' ]
				]
			],
			[
				'search-replace', 'http://public.tld', 'http://local.dev', 'wp_*_posts', 'wp_*_comments'
			]
		];

		$data[ 'dry_run' ] = [
			[
				'pattern' => 'http://public.tld',
				'replacement' => 'http://local.dev',
				'options' => [
					'dry_run' => true
				],
			],
			[
				'search-replace', 'http://public.tld', 'http://local.dev', '--dry-run'
			]
		];

		$data[ 'network' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'network' => true
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--network'
			]
		];

		$data[ 'all_tables_with_prefix' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'all_tables_with_prefix' => true
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--all-tables-with-prefix'
			]
		];

		$data[ 'all_tables' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'all_tables' => true
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--all-tables'
			]
		];

		$data[ 'precise' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'precise' => true
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--precise'
			]
		];

		$data[ 'recurse_objects' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'recurse_objects' => true
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--recurse-objects'
			]
		];

		$data[ 'regex' ] = [
			[
				'pattern' => 'https?://domain.tld',
				'replacement' => '//domain.tld',
				'options' => [
					'regex' => true
				],
			],
			[
				'search-replace', 'https?://domain.tld', '//domain.tld', '--regex'
			]
		];

		$data[ 'regex_flags' ] = [
			[
				'pattern' => 'https?://domain.tld',
				'replacement' => '//domain.tld',
				'options' => [
					'regex' => true,
					'regex_flags' => 'gi',
				],
			],
			[
				'search-replace', 'https?://domain.tld', '//domain.tld', '--regex', '--regex-flags=gi'
			]
		];

		$data[ 'skip_columns' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'skip_columns' => [ 'wp_posts', 'wp_options' ]
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--skip-columns=wp_posts,wp_options'
			]
		];
		$data[ 'skip_single_column' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'skip_columns' => [ 'wp_posts' ]
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--skip-columns=wp_posts'
			]
		];

		$data[ 'include_columns' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'include_columns' => [ 'wp_comments', 'wp_terms' ]
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--include-columns=wp_comments,wp_terms'
			]
		];

		$data[ 'include_single_column' ] = [
			[
				'pattern' => 'http://domain.tld',
				'replacement' => 'https://domain.tld',
				'options' => [
					'include_columns' => [ 'wp_comments' ]
				],
			],
			[
				'search-replace', 'http://domain.tld', 'https://domain.tld', '--include-columns=wp_comments'
			]
		];

		return $data;
	}
}
