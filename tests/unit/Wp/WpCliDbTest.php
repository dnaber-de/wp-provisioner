<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use MonkeryTestCase\MockeryTestCase;
use Mockery;
use WpProvision\Command\Command;

/**
 * Class WpCliDbTest
 *
 * @package WpProvision\Wp
 */
class WpCliDbTest extends MockeryTestCase  {

	/**
	 * @see WpCliDb::check()
	 */
	public function testCheck() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::create()
	 */
	public function testCreate() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::drop()
	 */
	public function testDrop() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::export()
	 */
	public function testExport() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::import()
	 */
	public function testImport() {

		$file = __FILE__;
		$arguments = [ 'db', 'import', realpath( $file ) ];
		$wp_cli = Mockery::mock( Command::class );
		$wp_cli->shouldReceive( 'run' )
			->with( $arguments );

		$testee = new WpCliDb( $wp_cli );
		$this->assertTrue(
			$testee->import( $file )
		);
	}

	/**
	 * @see WpCliDb::optimize()
	 */
	public function testOptimize() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::query()
	 */
	public function testQuery() {

		$query = "TRUNCATE TABLE wp_options";
		$expected_return = [];
		$expected_arguments = [ 'db', 'query', $query ];

		$wp_cli = Mockery::mock( Command::class );
		$wp_cli->shouldReceive( 'run' )
			->once()
			->with( $expected_arguments );

		$testee = new WpCliDb( $wp_cli );
		$this->assertSame(
			$expected_return,
			$testee->query( $query )
		);

	}

	/**
	 * @see WpCliDb::query()
	 */
	public function testQueryWithSqlArguments() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::repair()
	 */
	public function testRepair() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::reset()
	 */
	public function testReset() {
		// Todo
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::tables()
	 */
	public function testTables() {

		$patterns = [ '*comments*' ];
		$expected_arguments = array_merge(
			[ 'db', 'tables' ],
			$patterns
		);
		$expected_tables = [ 'wp_1_comments', 'wp_2_comments' ];
		$command_output = implode( PHP_EOL, array_merge( $expected_tables, [ '' ] ) );

		$wp_cli = Mockery::mock( Command::class );
		$wp_cli->shouldReceive( 'run' )
			->with( $expected_arguments )
			->andReturn( $command_output );

		$testee = new WpCliDb( $wp_cli );
		$this->assertSame(
			$expected_tables,
			$testee->tables( $patterns )
		);
	}

	/**
	 * @see WpCliDb::tables()
	 */
	public function testTablesWithOptions() {

		//Todo
		$this->markTestIncomplete( 'Under construction' );
	}
}
