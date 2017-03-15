<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use WpProvision\App\Command\Provision;
use WpProvision\Command\WpCli;
use WpProvision\Env\Bash;
use WpProvision\Process\ProcessBuilder;
use WpProvision\Process\SymfonyProcessBuilderAdapter;
use Symfony\Component\Console\Application;
use LogicException;

/**
 * Class WpProvisionerLoader
 *
 * @package WpProvision\Api
 */
final class WpProvisionerLoader implements WpProvisioner {

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
	 * @var WpCli
	 */
	private $wp_cli;

	/**
	 * @var WpCommandProvider
	 */
	private $provider;

	/**
	 * @param $base_dir
	 */
	public function __construct( $base_dir ) {

		$this->bootstrap( $base_dir );

		$wp_bin_path = $this->vendor_dir . '/bin/wp';
		if ( ! file_exists( $this->vendor_dir . '/bin/wp' ) ) {
			throw new LogicException( "WP executable not found in composer bin dir: '{$this->vendor_dir}/bin/wp'" );
		}
		$this->process_builder = new SymfonyProcessBuilderAdapter();
		$this->wp_cli          = new WpCli(
			new Bash( new SymfonyProcessBuilderAdapter ),
			$wp_bin_path,
			$this->process_builder
		);
		$this->provider = new WpCliCommandProvider( $this->wp_cli );
		$this->versions = new IsolatedVersions( $this->provider );
		$cwd = getcwd();

		$provison_file = $cwd . '/provision.php';
		if ( ! file_exists( $provison_file ) || ! is_readable( $provison_file ) ) {
			throw new LogicException( "Provision file not exists or is not readable '{$provison_file}'" );
		}

		$app = new Application( self::APP_NAME, self::APP_VERSION );
		$app->add( new Provision( $this->versions ) );
		$this->load_provision_file( $provison_file );

		$app->run();
	}

	/**
	 * @return Versions
	 */
	public function versionList() {

		return $this->versions;
	}

	/**
	 * @param $wp_dir
	 */
	public function setWpDir( $wp_dir ) {

		$this->process_builder->setWorkingDirectory( realpath( $wp_dir ) );
	}

	private function load_provision_file( $file ) {

		$api = $this;

		require $file;
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
