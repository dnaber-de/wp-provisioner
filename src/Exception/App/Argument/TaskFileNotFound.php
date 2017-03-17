<?php # -*- coding: utf-8 -*-

namespace WpProvision\Exception\App\Argument;

use WpProvision\Exception\RuntimeException;
use WpProvision\Exception\WpProvisionException;

/**
 * Class TaskFileNotFound
 *
 * @package WpProvision\Exception\App\Argument
 */
final class TaskFileNotFound extends RuntimeException implements WpProvisionException {}