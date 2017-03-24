<?php # -*- coding: utf-8 -*-

namespace WpProvision\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;
use MonkeryTestCase\MockeryTestCase;
use WpProvision\Command\Command;
use WpProvision\Command\GenericCommand;
use WpProvision\Container\Configurator;
use WpProvision\Env\Shell;
use WpProvision\Exception\Factory\WpCliNotFound;
use WpProvision\Process\ProcessBuilder;
use WpProvision\Process\SymfonyProcessBuilderAdapter;
use Mockery;
use ReflectionClass;

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

		$expectations = [
			'command' => [ 'wp' ],
			'cwd' => __DIR__,
		];

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );
		$process_builder = Mockery::mock( ProcessBuilder::class );

		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( 'wp' )
			->andReturn( true );
		$shell->shouldReceive( 'cwd' )
			->once()
			->andReturn( $expectations[ 'cwd' ] );

		$this->configureMocks( $container, $process_builder, $expectations );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$testee->getWpCliCommand();
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithWp() {

		$wp_cli = 'wp';
		$expectations = [
			'command' =>  [ $wp_cli ],
			'cwd' => __DIR__,
		];

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );
		$process_builder = Mockery::mock( ProcessBuilder::class );

		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( 'wp' )
			->andReturn( true );
		$shell->shouldReceive( 'cwd' )
			->once()
			->andReturn( $expectations[ 'cwd' ] );

		$this->configureMocks( $container, $process_builder, $expectations );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$testee->getWpCliCommand( 'wp' );

	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithAlias() {

		$alias = 'my-wp';
		$expectations = [
			'command' =>   [ $alias ],
			'cwd' => __DIR__,
		];

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );
		$process_builder = Mockery::mock( ProcessBuilder::class );

		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $alias )
			->andReturn( true );
		$shell->shouldReceive( 'cwd' )
			->once()
			->andReturn( $expectations[ 'cwd' ] );

		$this->configureMocks( $container, $process_builder, $expectations );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$testee->getWpCliCommand( $alias );

	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithExecutable() {

		$maybe_wp_cli = 'd:\foo\bar\wp.BAT';
		$expectations = [
			'command' => [ $maybe_wp_cli ],
			'cwd' => __DIR__,
		];

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );
		$process_builder = Mockery::mock( ProcessBuilder::class );

		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );
		$shell->shouldReceive( 'cwd' )
			->once()
			->andReturn( $expectations[ 'cwd' ] );
		$shell->shouldReceive( 'isReadable' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( true );
		$shell->shouldReceive( 'isExecutable' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( true );

		$this->configureMocks( $container, $process_builder, $expectations );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$testee->getWpCliCommand( $maybe_wp_cli );
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithPhpFile() {

		$maybe_wp_cli = 'd:\foo\bar\vendor\bin\wp';
		$php_executable = 'c:\bin\php.exe';
		$expectations = [
			'command' => [ $php_executable, $maybe_wp_cli ],
			'cwd' => __DIR__,
		];

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );
		$process_builder = Mockery::mock( ProcessBuilder::class );

		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );
		$shell->shouldReceive( 'cwd' )
			->once()
			->andReturn( $expectations[ 'cwd' ] );
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

		$this->configureMocks( $container, $process_builder, $expectations );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$testee->getWpCliCommand( $maybe_wp_cli );
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandWithWpFallback() {

		$maybe_wp_cli = 'd:\foo\bar\vendor\bin\wp';
		$wp_executable = 'c:\bin\wp.bat';
		$expectations = [
			'command' => [ $wp_executable ],
			'cwd' => __DIR__,
		];

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );
		$process_builder = Mockery::mock( ProcessBuilder::class );

		$shell->shouldReceive( 'commandExists' )
			->once()
			->with( $maybe_wp_cli )
			->andReturn( false );
		$shell->shouldReceive( 'cwd' )
			->once()
			->andReturn( $expectations[ 'cwd' ] );
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

		$this->configureMocks( $container, $process_builder, $expectations );

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$testee->getWpCliCommand( $maybe_wp_cli );
	}

	/**
	 * @see WpCliCommandFactory::getWpCliCommand()
	 */
	public function testGetWpCliCommandThrowsException() {

		$php_finder = Mockery::mock( PhpExecutableFinder::class );
		$exec_finder = Mockery::mock( ExecutableFinder::class );
		$shell = Mockery::mock( Shell::class );
		$container = Mockery::mock( ContainerInterface::class );

		$shell->shouldReceive( 'commandExists' )
			->andReturn( false );
		$shell->shouldReceive( 'cwd' )
			->andReturn( __DIR__ );
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

		$testee = new WpCliCommandFactory( $php_finder, $exec_finder, $shell, $container );
		$this->expectException( WpCliNotFound::class );
		$testee->getWpCliCommand( 'whatever' );
	}

	/**
	 * @param Mockery\MockInterface $container
	 * @param Mockery\MockInterface $process_builder
	 * @param array $expectations
	 */
	private function configureMocks(
		Mockery\MockInterface $container,
		Mockery\MockInterface $process_builder,
		array $expectations
	) {


		$container->shouldReceive( 'get' )
			->once()
			->with( Configurator::WP_CLI_PROCESS_BUILDER )
			->andReturn( $process_builder );

		$process_builder->shouldReceive( 'setPrefix' )
			->once()
			->with( Mockery::on( function( $prefix ) use ( $expectations ) {
				$this->assertSame(
					$expectations[ 'command' ],
					$prefix
				);
				return true;
			} ) );
		$process_builder->shouldReceive( 'setWorkingDirectory' )
			->once()
			->with( Mockery::on( function( $cwd ) use ( $expectations ) {
				$this->assertSame(
					$expectations[ 'cwd' ],
					$cwd
				);
				return true;
			} ) );
	}
}
