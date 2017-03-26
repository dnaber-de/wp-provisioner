<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use WpProvision\Api\WpProvisionerLoader;
use WpProvision\App\Command\Provision;
use WpProvision\App\Command\Task;

/**
 * Class DiceConfigurator
 *
 * @package WpProvision\Container
 */
final class DiceAppConfigurator implements Configurator {

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct( ContainerInterface $container ) {

		$this->container = $container;
	}

	/**
	 * @param DiceConfigurable $dice
	 */
	public function configure( DiceConfigurable $dice ) {

		$container = $this->container;

		$dice->addRule(
			Application::class,
			[
				'constructorParams' => [
					WpProvisionerLoader::APP_NAME,
					WpProvisionerLoader::APP_VERSION
				]
			]
		);

		// Container for the command sub system
		$dice->addRule(
			'$apiContainer',
			[
				'shared' => true,
				'instanceOf' => DiceContainer::class
			]
		);


		$dice->addRule(
			Provision::class,
			[
				'substitutions' => [
					ContainerInterface::class => [
						'instance' => function() use ( $container ) {
							return $container->get( '$apiContainer' );
						}
					],
					DiceConfigurable::class => [
						'instance' => function() use ( $container ) {
							return $container->get( '$apiContainer' );
						}
					],
					Configurator::class => [
						'instance' => function() {
							return null; // Optional type-hinted dependency
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

	}

}