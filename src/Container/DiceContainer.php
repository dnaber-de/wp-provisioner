<?php # -*- coding: utf-8 -*-

namespace WpProvision\Container;

use Dice\Dice;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use WpProvision\Exception\Container\NotFoundException;

/**
 * Class DiceContainer
 *
 * @package WpProvision\Container
 */
final class DiceContainer implements ContainerInterface, DiceConfigurable {

	/**
	 * @var Dice
	 */
	private $dice;

	/**
	 * @param Dice $dice
	 */
	public function __construct( Dice $dice ) {

		$this->dice = $dice;
	}

	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
	 * @throws ContainerExceptionInterface Error while retrieving the entry.
	 *
	 * @return mixed Entry.
	 */
	public function get( $id ) {

		if ( $this->has( $id ) ) {
			return $this->dice->create( $id );
		}

		throw new NotFoundException( "Could not create instance of {$id}" );
	}

	/**
	 * Returns true if the container can return an entry for the given identifier.
	 * Returns false otherwise.
	 *
	 * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
	 * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
	 *
	 * @param string $id Identifier of the entry to look for.
	 *
	 * @return bool
	 */
	public function has( $id ) {
		return class_exists( $id ) || $this->dice->getRule( $id ) != $this->dice->getRule( '*' );
	}

	/**
	 * @param string $id
	 * @param array $rule
	 *
	 * @return void
	 */
	public function addRule( $id, array $rule ) {

		$this->dice->addRule( $id, $rule );
	}

	/**
	 * @param string $id
	 *
	 * @return array
	 */
	public function getRule( $id ) {

		return $this->dice->getRule( $id );
	}

}