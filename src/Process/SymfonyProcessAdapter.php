<?php # -*- coding: utf-8 -*-

namespace WpProvision\Process;

use
	Symfony\Component\Process\Process as SymfonyProcess;

/**
 * Class SymfonyProcessAdapter
 *
 * Adapts the Process\Process interface with the Symfony Process implementation
 *
 * @package WpProvision\Process
 */
class SymfonyProcessAdapter
	extends SymfonyProcess
	implements Process {}
