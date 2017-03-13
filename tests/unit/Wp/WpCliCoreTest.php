<?php # -*- coding: utf-8 -*-

namespace WpProvision\Wp;

use WpProvision\Command\WpCliCommand;
use WpProvision\Utils\PasswordGenerator;
use Mockery;
use MonkeryTestCase;

class WpCliCoreTest extends MonkeryTestCase\MockeryTestCase {

	/**
	 * @see WpCliCore::isInstalled
	 */
	public function testIsInstalled() {

		$wp_cli_mock = Mockery::mock( WpCliCommand::class );
		$wp_cli_mock->shouldReceive( 'run' )
			->with( [ 'core', 'is-installed' ] );

		$testee = new WpCliCore(
			$wp_cli_mock,
			Mockery::mock( PasswordGenerator::class )
		);

		$this->assertTrue( $testee->isInstalled() );
	}

	/**
	 * @see WpCliCore::isInstalled
	 */
	public function testIsInstalledNetwork() {

		$wp_cli_mock = Mockery::mock( WpCliCommand::class );
		$wp_cli_mock->shouldReceive( 'run' )
			->with( [ 'core', 'is-installed', '--network' ] );

		$testee = new WpCliCore(
			$wp_cli_mock,
			Mockery::mock( PasswordGenerator::class )
		);

		$this->assertTrue( $testee->isInstalled( TRUE ) );
	}

	/**
	 * @see WpCliCore::isInstalled
	 */
	public function testIsInstalledException() {

		$wp_cli_mock = Mockery::mock( WpCliCommand::class );
		$wp_cli_mock->shouldReceive( 'run' )
			->with( [ 'core', 'is-installed' ] );

		$testee = new WpCliCore(
			$wp_cli_mock,
			Mockery::mock( PasswordGenerator::class )
		);

		$this->markAsRisky();
		#$this->assertTrue( $testee->isInstalled( TRUE ) );
	}
}
