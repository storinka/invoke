<?php

namespace Invoke\Extensions;

use Invoke\Invoke;
use Psr\Container\ContainerInterface;

/**
 * Invoke extension interface.
 */
interface Extension
{
    /**
     * This hook is called when "serve" method is called in {@see Invoke}.
     *
     * @param Invoke $invoke
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(Invoke $invoke, ContainerInterface $container): void;
}