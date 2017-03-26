<?php # -*- coding: utf-8 -*-

namespace WpProvision\Exception\Wp;

use WpProvision\Exception\WpProvisionException;

/**
 * Class InvalidArgumentException
 *
 * @package WpProvision\Exception\Wp
 */
class InvalidArgumentException extends \InvalidArgumentException implements WpProvisionException {}