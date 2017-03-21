<?php # -*- coding: utf-8 -*-

namespace WpProvision\Factory;

use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use MonkeryTestCase\MockeryTestCase;
use Symfony\Component\Process\ProcessBuilder;
use WpProvision\Command\Command;
use WpProvision\Command\GenericCommand;
use WpProvision\Env\Shell;
use Mockery;
use ReflectionClass;
use WpProvision\Exception\Factory\WpCliNotFound;
use WpProvision\Process\SymfonyProcessBuilderAdapter;

/**
 * Class WpCliCommandFactoryTest
 *
 * @package WpProvision\Factory
 */
class WpCliCommandFactoryTest extends MockeryTestCase  {

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandDefault() {

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( 'wp' )
			->andReturn( true );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$command = $testee->getWpCliCommand();

		$this->assertSame(
			[ 'wp' ],
			$this->getCommandBase( $command )
		);
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithWp() {

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( 'wp' )
			->andReturn( true );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$command = $testee->getWpCliCommand( 'wp' );

		$this->assertSame(
			[ 'wp' ],
			$this->getCommandBase( $command )
		);
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithAlias() {

		$alias = 'my-wp';
		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $alias )
			->andReturn( true );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$command = $testee->getWpCliCommand( $alias );

		$this->assertSame(
			[ $alias ],
			$this->getCommandBase( $command )
		);
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithExecutable() {

		$maybe_wp_cli = 'd:\foo\bar\wp.BAT';
		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );
		$shell->shouldReceive( 'isReadable' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( true );
		$shell->shouldReceive( 'isExecutable' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( true );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$command = $testee->getWpCliCommand( $maybe_wp_cli );

		$this->assertSame(
			[ $maybe_wp_cli ],
			$this->getCommandBase( $command )
		);
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithPhpFile() {

		$maybe_wp_cli = 'd:\foo\bar\vendor\bin\wp';
		$php_executable = 'c:\bin\php.exe';
		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );
		$shell->shouldReceive( 'isReadable' )
			->atLeast( 1 )
			->with( $maybe_wp_cli )
			->andReturn( true );
		$shell->shouldReceive( 'isExecutable' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );

		$php_finder->shouldReceive( 'find' )
			->once()
			->andReturn( $php_executable );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$command = $testee->getWpCliCommand( $maybe_wp_cli );

		$this->assertSame(
			[ $php_executable, $maybe_wp_cli ],
			$this->getCommandBase( $command )
		);
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithWpFallback() {

		$maybe_wp_cli = 'd:\foo\bar\vendor\bin\wp';
		$wp_executable = 'c:\bin\wp.bat';
		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );
		$shell->shouldReceive( 'isReadable' )
			->atLeast( 1 )
			->with( $maybe_wp_cli )
			->andReturn( true );
		$shell->shouldReceive( 'isExecutable' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );

		$php_finder->shouldReceive( 'find' )
			->once()
			->andReturn( false );

		$exec_finder->shouldReceive( 'find' )
			->once()
			->with( 'wp' )
			->andReturn( $wp_executable );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$command = $testee->getWpCliCommand( $maybe_wp_cli );

		$this->assertSame(
			[ $wp_executable ],
			$this->getCommandBase( $command )
		);
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandThrowsException() {

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$shell->shouldReceive( 'commandExists' )
			->andReturn( false );
		$shell->shouldReceive( 'isReadable' )
			->andReturn( false );
		$shell->shouldReceive( 'isExecutable' )
			->andReturn( false );

		$php_finder->shouldReceive( 'find' )
			->once()
			->andReturn( false );

		$exec_finder->shouldReceive( 'find' )
			->once()
			->with( 'wp' )
			->andReturn( false );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell );
		$this->expectException( WpCliNotFound::class );
		$command = $testee->getWpCliCommand( 'whatever' );

		var_dump( $this->getCommandBase( $command ) );
	}

	/**
	 * @param Command $command
	 *
	 * @return array
	 */
	private function getCommandBase( Command $command ) {

		$command_reflection = new ReflectionClass( GenericCommand::class );
		$process_builder_property = $command_reflection->getProperty( 'process_builder' );
		$process_builder_property->setAccessible( true );
		$process_builder = $process_builder_property->getValue( $command );
		/** @var SymfonyProcessBuilderAdapter $process_builder */
		$process_builder_reflection = new ReflectionClass( ProcessBuilder::class );
		$base_property = $process_builder_reflection->getProperty( 'prefix' );
		$base_property->setAccessible( true );

		return $base_property->getValue( $process_builder );
	}
}
