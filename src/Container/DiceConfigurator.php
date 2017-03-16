<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

use Psr\Container\ContainerInterface;
use WpProvision\Command\WpCli;

/**
 * Class DiceConfigurator
 *
 * @package WpProvision\Container
 */
final class DiceConfigurator implements Configurator {

	use DiceConfiguration;

	/**
	 * @var DiceConfigurable
	 */
	private $dice;

	/**
	 * @param DiceConfigurable $dice
	 */
	public function __construct( DiceConfigurable $dice, ContainerInterface $container ) {

		$this->dice = $dice;
		$this->setup( $dice, $container, $this );
	}

	/**
	 * @param string $file
	 */
	public function setWpCliExecutable( $file ) {

		$this->dice->addRule(
			WpCli::class,
			[
				'constructParameters' => [ $file ]
			]
		);
	}
}