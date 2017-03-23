<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

use Psr\Container\ContainerInterface;
use WpProvision\Command\GenericCommand;

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
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * @param DiceConfigurable $dice
	 * @param ContainerInterface $container
	 */
	public function __construct( DiceConfigurable $dice, ContainerInterface $container ) {

		$this->dice = $dice;
		$this->container = $container;
	}

	/**
	 * @param void
	 */
	public function setup() {

		if ( $this->loaded ) {
			return;
		}

		$this->setup_dice( $this->dice, $this->container );
		$this->loaded = true;
	}

}