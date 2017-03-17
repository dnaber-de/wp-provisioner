<?php # -*- coding: utf-8 -*-

namespace WpProvision\Api;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SymfonyOutputAdapter
 *
 * @package WpProvision\Api
 */
final class SymfonyOutputAdapter implements ConsoleOutput {

	/**
	 * @var OutputInterface
	 */
	private $output;

	/**
	 * @param OutputInterface $output
	 */
	public function __construct( OutputInterface $output ) {

		$this->output = $output;
	}

	/**
	 * @param array $messages
	 * @param bool $newline
	 *
	 * @return void
	 */
	public function write( array $messages, $newline = false ) {

		$this->output->write( $messages, $newline );
	}

	/**
	 * @param array $messages
	 *
	 * @return void
	 */
	public function writeln( array $messages ) {

		$this->output->writeln( $messages );
	}
}