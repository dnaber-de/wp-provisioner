<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use WpProvision\Api\IsolatedVersions;
use WpProvision\Api\WpCliCommandProvider;
use WpProvision\Api\WpCommandProvider;
use WpProvision\Api\WpProvisionerLoader;
use WpProvision\App\Command\Provision;
use WpProvision\App\Command\Task;
use WpProvision\Command\GenericCommand;
use WpProvision\Command\WpCliCommand;
use WpProvision\Env\Bash;
use WpProvision\Env\Shell;
use WpProvision\Env\Windows;
use WpProvision\Process\ProcessBuilder;
use WpProvision\Process\SymfonyProcessBuilderAdapter;
use WpProvision\Utils\PasswordGenerator;
use WpProvision\Utils\Sha1PasswordGenerator;
use WpProvision\Wp\Db;
use WpProvision\Wp\Plugin;
use WpProvision\Wp\Site;
use WpProvision\Wp\User;
use WpProvision\Wp\WpCliCli;
use WpProvision\Wp\WpCliCore;
use WpProvision\Wp\WpCliDb;
use WpProvision\Wp\WpCliPlugin;
use WpProvision\Wp\WpCliSite;
use WpProvision\Wp\WpCliUser;

trait DiceConfiguration {

	/**
	 * @param DiceConfigurable $dice
	 * @param ContainerInterface $container
	 *
	 * @return void
	 */
	private function setup_dice(
		DiceConfigurable $dice,
		ContainerInterface $container
	) {

		/**
		 * Process builder should only shared inside a single object graph, not globally
		 */
		$dice->addRule(
			'$symfonyProcessBuilder',
			[
				'shared' => true,
				'instanceOf' => SymfonyProcessBuilderAdapter::class
			]
		);

		$dice->addRule(
			Application::class,
			[
				'constructorParams' => [
					WpProvisionerLoader::APP_NAME,
					WpProvisionerLoader::APP_VERSION
				]
			]
		);

		$dice->addRule(
			GenericCommand::class,
			[
				'shared' => true,
				'substitutions' => [
					Shell::class => [
						'instance' => function() {
							return 'WIN' === strtoupper( substr( PHP_OS, 0, 3 ) )
								? new Windows( new SymfonyProcessBuilderAdapter() )
								: new Bash( new SymfonyProcessBuilderAdapter() );
						}
					],
					ProcessBuilder::class => [
						'instance' => '$symfonyProcessBuilder'
					]
				],
				'constructParams' => [ 'wp' ]
			]
		);

		$dice->addRule(
			IsolatedVersions::class,
			[
				'shared' => true,
				'substitutions' => [
					WpCommandProvider::class => [
						'instance' => WpCliCommandProvider::class
					]
				]
			]
		);
		$dice->addRule(
			WpCliCommandProvider::class,
			[
				'shared' => true,
				'substitutions' => [
					ContainerInterface::class => [
						'instance' => function() use ( $container ) {
							return $container;
						}
					]
				]
			]
		);
		$dice->addRule(
			Provision::class,
			[
				'substitutions' => [
					ContainerInterface::class => [
						'instance' => function() use ( $container ) {
							return $container;
						}
					]
				]
			]
		);
		$dice->addRule(
			Task::class,
			[
				'substitutions' => [
					ContainerInterface::class => [
						'instance' => function() use ( $container ) {
							return $container;
						}
					]
				]
			]
		);

		$commands = [
			WpCliCli::class,
			WpCliCore::class,
			WpCliPlugin::class,
			WpCliSite::class,
			WpCliUser::class,
			WpCliDb::class
		];
		foreach ( $commands as $class ) {
			$dice->addRule(
				$class,
				[
					'substitutions' => [
						WpCliCommand::class => [
							'instance' => GenericCommand::class
						],
						User::class => [
							'instance' => WpCliUser::class
						],
						Plugin::class => [
							'instance' => WpCliPlugin::class
						],
						Site::class => [
							'instance' => WpCliSite::class
						],
						Db::class => [
							'instance' => WpCliDb::class,
						],
						PasswordGenerator::class => [
							'instance' => Sha1PasswordGenerator::class
						]
					]
				]
			);
		}
	}
}