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
	implements ProcessBuilder {}
