<?php # -*- coding: utf-8 -*-

namespace WpProvision\Env;

use
	WpProvision\Process;

/**
 * Class Bash
 *
 * @package WpProvision\Env
 */
class Bash implements Shell {

	/**
	 * @var Process\ProcessBuilder
	 */
	private $processBuilder;

	/**
	 * @param Process\ProcessBuilder $processBuilder
	 */
	public function __construct( Process\ProcessBuilder $processBuilder ) {

		$this->processBuilder = $processBuilder;
	}

	/**
	 * @param $command
	 *
	 * @return bool
	 */
	public function commandExists( $command ) {

		$process = $this
			->processBuilder
			->setArguments(
				[
					'hash',
					$command,
					'2>/dev/null || echo "false"'
				]
			)
			->getProcess();

		$output = $process
			->mustRun()
			->getOutput();

		return "false" !== trim( $output );
	}

}
