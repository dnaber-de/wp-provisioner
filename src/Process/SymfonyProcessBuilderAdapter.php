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
	 * MUST return a new instance of process builder instead of
	 * changing state of the current one.
	 *
	 * @param $cwd
	 *
	 * @return ProcessBuilder
	 */
	public function withWorkingDirectory( $cwd ) {

		$process_builder = clone $this;
		$process_builder->setWorkingDirectory( $cwd );

		return $process_builder;
	}

	/**
	 * MUST return a new instance of process builder instead of
	 * changing state of the current one.
	 *
	 * @param array $prefix
	 *
	 * @return ProcessBuilder
	 */
	public function withPrefix( array $prefix ) {

		$process_builder = clone $this;
		$process_builder->setPrefix( $prefix );

		return $process_builder;
	}

}
