<?php # -*- coding: utf-8 -*-

namespace WpProvision\Process;

/**
 * Interface Process
 *
 * Interface for Symfony\Components\Process\ProcessBuilder
 *
 * @see \Symfony\Component\Process\ProcessBuilder
 *
 * @package WpProvision\Process
 */
interface ProcessBuilder {

	/**
	 * @see \Symfony\Component\Process\ProcessBuilder::create()
	 *
	 * @param array $arguments
	 *
	 * @return ProcessBuilder
	 */
	public static function create( array $arguments = [] );

	/**
	 * @see \Symfony\Component\Process\ProcessBuilder::setArguments()
	 *
	 * @param array $arguments
	 *
	 * @return self
	 */
	public function setArguments( array $arguments );

	/**
	 * @see \Symfony\Component\Process\ProcessBuilder::getProcess()
	 *
	 * @return Process
	 *
	 * @throws \LogicException In case no arguments have been provided
	 */
	public function getProcess();

	/**
	 * Sets the working directory.
	 *
	 * @param null|string $cwd The working directory
	 *
	 * @return ProcessBuilder
	 */
	public function setWorkingDirectory( $cwd );

	/**
	 * @param array $prefix
	 *
	 * @return ProcessBuilder
	 */
	public function setPrefix( array $prefix );

	/**
	 * MUST return a new instance of process builder instead of
	 * changing state of the current one.
	 *
	 * @param $cwd
	 *
	 * @return ProcessBuilder
	 */
	public function withWorkingDirectory( $cwd );

	/**
	 * MUST return a new instance of process builder instead of
	 * changing state of the current one.
	 *
	 * @param array $prefix
	 *
	 * @return ProcessBuilder
	 */
	public function withPrefix( array $prefix );
}
