<?php # -*- coding: utf-8 -*-

namespace WpProvision\Process;

use Symfony\Component\Process\ProcessBuilder as SymfonyProcessBuilder;

/**
 * Class SymfonyProcessBuilderAdapter
 *
 * Adapts the Process\ProcessBuilder interface with the
 * Symfony ProcessBuilder implementation
 *
 * @package WpProvision\Process
 */
final class SymfonyProcessBuilderAdapter
	extends SymfonyProcessBuilder
	implements ProcessBuilder {

	/**
	 * @param string|array $prefix
	 *
	 * @return ProcessBuilder
	 */
	public function setPrefix( $prefix ) {

		parent::setPrefix( $prefix );

		return $this;
	}

	/**
	 * MUST return a new instance of process builder instead of
	 * changing state of the current one.
	 *
	 * @param string $cwd
	 *
	 * @return ProcessBuilder
	 */
	public function withWorkingDirectory( $cwd ) {

		$process_builder = clone $this;
		$process_builder->setWorkingDirectory( (string) $cwd );

		return $process_builder;
	}

	/**
	 * MUST return a new instance of process builder instead of
	 * changing state of the current one.
	 *
	 * @param string|array $prefix
	 *
	 * @return ProcessBuilder
	 */
	public function withPrefix( $prefix ) {

		$process_builder = clone $this;
		$process_builder->setPrefix( $prefix );

		return $process_builder;
	}

}
