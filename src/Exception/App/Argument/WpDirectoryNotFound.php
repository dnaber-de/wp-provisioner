<?php # -*- coding: utf-8 -*-

namespace WpProvision\Exception\App\Argument;

use WpProvision\Exception\RuntimeException;
use WpProvision\Exception\WpProvisionException;

/**
 * Class WpDirectoryNotFound
 *
 * @package WpProvision\Exception\App\Argument
 */
class WpDirectoryNotFound extends RuntimeException implements WpProvisionException {}