<?php

namespace Invoke\Extensions;

use Invoke\Invoke;
use Psr\Container\ContainerInterface;

/**
 * Invoke extension interface.
 */
interface Extension
{
    public function boot(Invoke $invoke, ContainerInterface $container): void;
}