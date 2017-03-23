<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use Dice\Dice;
use WpProvision\App\Command\Provision;
use WpProvision\App\Command\Task;
use WpProvision\Command\GenericCommand;
use WpProvision\Container\DiceConfigurator;
use WpProvision\Container\DiceContainer;
use WpProvision\Exception\Api\TaskFileNotFound;
use WpProvision\Exception\Api\TaskFileReturnsNoCallable;
use WpProvision\Process\ProcessBuilder;
use Symfony\Component\Console\Application;
use WpProvision\Process\SymfonyProcessBuilderAdapter;

/**
 * Class WpProvisionerLoader
 *
 * @package WpProvision\Api
 */
class WpProvisionerLoader {

	const APP_VERSION = 'dev-master';
	const APP_NAME = 'WP Provisioner';

	/**
	 * @var string
	 */
	private $vendor_dir;

	/**
	 * @var Versions
	 */
	private $versions;

	/**
	 * @var ProcessBuilder
	 */
	private $process_builder;

	/**
	 * @var GenericCommand
	 */
	private $wp_cli;

	/**
	 * @var WpCommandProvider
	 */
	private $provider;

	/**
	 * @param string $base_dir
	 */
	public function __construct( $base_dir ) {

		$this->bootstrap( $base_dir );

		$container = new DiceContainer( new Dice() );
		( new DiceConfigurator( $container, $container ) )-> setup();

		$app = $container->get( Application::class );
		$app->add( $container->get( Provision::class ) );
		$app->add( $container->get( Task::class ) );

		$app->run();
	}


	/**
	 * Todo: Exclude this
	 *
	 * @param string $base_dir Path of the libraries root directory
	 */
	private function bootstrap( $base_dir ) {

		// when installed separately as »project«
		$autoload_project = $base_dir . '/vendor/autoload.php';
		// when installed as dependency
		$autoload_library = dirname( // /vendor
			dirname(                 // /dnaber
				$base_dir            // /wp-provisioner
			)
		) . '/autoload.php';

		if ( file_exists( $autoload_project ) ) {
			$this->vendor_dir = $base_dir . '/vendor';
			require_once $autoload_project;
		} elseif( file_exists( $autoload_library ) ) {
			$this->vendor_dir = dirname( dirname( $base_dir ) );
			require_once $autoload_library;
		} else {
			echo "Composer autoload file not found\n";
			exit( 1 );
		}
	}
}
