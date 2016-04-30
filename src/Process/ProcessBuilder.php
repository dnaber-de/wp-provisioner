<?php # -*- coding: utf-8 -*-

namespace WpProvision\Process;

/**
 * Interface Process
 *
 * Interface for Symfony\Components\Process\ProcessBuilder
 *
 * @package WpProvision\Process
 */
interface ProcessBuilder {

	/**
	 * @see Symfony\Components\Process\ProcessBuilder
	 *
	 * @return Process
	 */
	public function getProcess();

}
