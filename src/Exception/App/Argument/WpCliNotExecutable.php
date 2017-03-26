<?php # -*- coding: utf-8 -*-

namespace WpProvision\Exception\App\Argument;

use WpProvision\Exception\RuntimeException;
use WpProvision\Exception\WpProvisionException;

/**
 * Class WpCliNotExecutable
 *
 * @package WpProvision\Exception\App\Argument
 */
class WpCliNotExecutable extends RuntimeException implements WpProvisionException {}