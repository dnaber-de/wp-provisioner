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
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::create()
	 */
	public function testCreate() {
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::drop()
	 */
	public function testDrop() {
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::export()
	 */
	public function testExport() {
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::import()
	 */
	public function testImport() {

		$file = __FILE__;
		$arguments = [ 'import', $file ];
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
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::query()
	 */
	public function testQuery() {
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::repair()
	 */
	public function testRepair() {
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::reset()
	 */
	public function testReset() {
		$this->markTestIncomplete( 'Under construction' );
	}

	/**
	 * @see WpCliDb::tables()
	 */
	public function testTables() {
		$this->markTestIncomplete( 'Under construction' );
	}
}
