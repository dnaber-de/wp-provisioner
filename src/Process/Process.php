<?php # -*- coding: utf-8 -*-

namespace WpProvision\Process;

/**
 * Interface Process
 *
 * Incomplete interface for Symfony\Component\Process\Process
 *
 * @see Symfony\Component\Process\Process
 * @package WpProvision\Process
 */
interface Process {

	/**
	 * @param int|float|null $timeout The timeout in seconds
	 *
	 * @see Symfony\Component\Process\Process::setTimeout()
	 *
	 * @return self
	 *
	 * @throws \InvalidArgumentException if the timeout is negative
	 */
	public function setTimeout( $timeout );

	/**
	 * @param int|float|null $timeout The timeout in seconds
	 *
	 * @see Symfony\Component\Process\Process::setIdleTimeout()
	 *
	 * @return self
	 *
	 * @throws \LogicException           if the output is disabled
	 * @throws \InvalidArgumentException if the timeout is negative
	 */
	public function setIdleTimeout( $timeout );

	/**
	 * @param callable|NULL $callback
	 *
	 * @see Symfony\Component\Process\Process::mustRun()
	 *
	 * @return self
	 */
	public function mustRun( callable $callback = null );

	/**
	 * @see Symfony\Component\Process\Process::getOutput()
	 *
	 * @return string
	 *
	 * @throws \LogicException in case the output has been disabled or the process is not started
	 */
	public function getOutput();
}
