<?php # -*- coding: utf-8 -*-
declare( strict_types = 1 );

namespace WpProvision\Wp;

use MonkeryTestCase\BrainMonkeyWpTestCase;
use WpProvision\Command\Command;

class WpCliSiteTest extends BrainMonkeyWpTestCase {

	/**
	 * @dataProvider listData
	 */
	public function testList( array $options, array $command, string $stdout, array $expected ) {

		$commandMock = \Mockery::mock( Command::class );
		$user = \Mockery::mock( User::class );
		$plugin = \Mockery::mock( Plugin::class );

		$argumentMatcher = function( $arg ) use ( $command ) {
			self::assertSame(
				$command,
				$arg
			);

			return true;
		};

		$commandMock->shouldReceive( 'run' )
			->once()
			->with( \Mockery::on( $argumentMatcher ) )
			->andReturn( $stdout );

		$testee = new WpCliSite( $commandMock, $user, $plugin );

		self::assertSame(
			$expected,
			$testee->list( $options )
		);
	}

	/**
	 * @see testList
	 */
	public function listData() : array {

		$output_all_sites = <<<STDOUT
blog_id\turl\tlast_updated\tregistered
1\thttp://example.dev/\t2017-07-11 13:45:06\t2016-05-18 20:58:35
2\thttp://example.dev/blog/\t2017-07-11 15:00:54\t2016-05-18 21:02:55
3\thttp://example.dev.uk/\t2016-10-23 19:34:53\t2016-05-18 21:02:59
4\thttp://example.dev.uk/blog/\t2017-07-11 15:01:01\t2016-05-18 21:03:03
STDOUT;

		$output_site_1 = <<<STDOUT
blog_id\turl\tlast_updated\tregistered
1\thttp://example.dev/\t2017-07-11 13:45:06\t2016-05-18 20:58:35
STDOUT;

		$output_all_sites_two_columns = <<<STDOUT
url\tblog_id
http://example.dev/\t1
http://example.dev/blog/\t2
http://example.dev.uk/\t3
http://example.dev.uk/blog/\t4
STDOUT;

		$output_all_sites_single_column = <<<STDOUT
http://example.dev/
http://example.dev/blog/
http://example.dev.uk/
http://example.dev.uk/blog/
STDOUT;


		return [
			[
				[],
				[ 'site', 'list' ],
				$output_all_sites,
				[
					[
						'blog_id' => '1',
						'url' => 'http://example.dev/',
						'last_updated' => '2017-07-11 13:45:06',
						'registered' => '2016-05-18 20:58:35',
					],
					[
						'blog_id' => '2',
						'url' => 'http://example.dev/blog/',
						'last_updated' => '2017-07-11 15:00:54',
						'registered' => '2016-05-18 21:02:55',
					],
					[
						'blog_id' => '3',
						'url' => 'http://example.dev.uk/',
						'last_updated' => '2016-10-23 19:34:53',
						'registered' => '2016-05-18 21:02:59',
					],
					[
						'blog_id' => '4',
						'url' => 'http://example.dev.uk/blog/',
						'last_updated' => '2017-07-11 15:01:01',
						'registered' => '2016-05-18 21:03:03',
					],
				]
			],
			[
				[ 'network_id' => 2 ],
				[ 'site', 'list', '--network=2' ],
				$output_all_sites,
				[
					[
						'blog_id' => '1',
						'url' => 'http://example.dev/',
						'last_updated' => '2017-07-11 13:45:06',
						'registered' => '2016-05-18 20:58:35',
					],
					[
						'blog_id' => '2',
						'url' => 'http://example.dev/blog/',
						'last_updated' => '2017-07-11 15:00:54',
						'registered' => '2016-05-18 21:02:55',
					],
					[
						'blog_id' => '3',
						'url' => 'http://example.dev.uk/',
						'last_updated' => '2016-10-23 19:34:53',
						'registered' => '2016-05-18 21:02:59',
					],
					[
						'blog_id' => '4',
						'url' => 'http://example.dev.uk/blog/',
						'last_updated' => '2017-07-11 15:01:01',
						'registered' => '2016-05-18 21:03:03',
					],
				]
			],
			[
				[ 'site_in' => [ 1, 2, 3, 4 ] ],
				[ 'site', 'list', '--site__in=1,2,3,4' ],
				$output_all_sites,
				[
					[
						'blog_id' => '1',
						'url' => 'http://example.dev/',
						'last_updated' => '2017-07-11 13:45:06',
						'registered' => '2016-05-18 20:58:35',
					],
					[
						'blog_id' => '2',
						'url' => 'http://example.dev/blog/',
						'last_updated' => '2017-07-11 15:00:54',
						'registered' => '2016-05-18 21:02:55',
					],
					[
						'blog_id' => '3',
						'url' => 'http://example.dev.uk/',
						'last_updated' => '2016-10-23 19:34:53',
						'registered' => '2016-05-18 21:02:59',
					],
					[
						'blog_id' => '4',
						'url' => 'http://example.dev.uk/blog/',
						'last_updated' => '2017-07-11 15:01:01',
						'registered' => '2016-05-18 21:03:03',
					],
				]
			],
			[
				[ 'filter' => [ 'url' => 'http://example.dev/' ] ],
				[ 'site', 'list', '--url=http://example.dev/' ],
				$output_site_1,
				[
					[
						'blog_id' => '1',
						'url' => 'http://example.dev/',
						'last_updated' => '2017-07-11 13:45:06',
						'registered' => '2016-05-18 20:58:35',
					],
				]
			],
			[
				[ 'fields' => [ 'url', 'blog_id' ] ],
				[ 'site', 'list', '--fields=url,blog_id' ],
				$output_all_sites_two_columns,
				[
					[
						'url' => 'http://example.dev/',
						'blog_id' => '1',
					],
					[
						'url' => 'http://example.dev/blog/',
						'blog_id' => '2',
					],
					[
						'url' => 'http://example.dev.uk/',
						'blog_id' => '3',
					],
					[
						'url' => 'http://example.dev.uk/blog/',
						'blog_id' => '4',
					],
				]
			],
			[
				[ 'fields' => [ 'blog_id' ] ],
				[ 'site', 'list', '--field=blog_id' ],
				$output_all_sites_single_column,
				[
					'http://example.dev/',
					'http://example.dev/blog/',
					'http://example.dev.uk/',
					'http://example.dev.uk/blog/',
				]
			],
			[
				[
					'network_id' => 2,
					'site_in' => [ 1, 2 ],
					'filter' => [
						'url' => 'http://noexist.tld',
					],
					'fields' => [ 'blog_id', 'url' ],
				],
				[
					'site',
					'list',
					'--network=2',
					'--site__in=1,2',
					'--fields=blog_id,url',
					'--url=http://noexist.tld',
				],
				'',
				[]
			],
		];

	}
}
