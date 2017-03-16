<?php # -*- coding: utf-8 -*-

namespace WpProvision\Exception\Container;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface {}