<?php

namespace Invoke\Extensions;

use Invoke\InvokeInterface;
use Psr\Container\ContainerInterface;

/**
 * Invoke extension interface.
 */
interface Extension
{
    /**
     * This hook is called when "serve" method is called in {@see InvokeInterface}.
     *
     * @param InvokeInterface $invoke
     * @param ContainerInterface $container
     * @return void
     */
    public function load(InvokeInterface $invoke, ContainerInterface $container): void;

    /**
     * @param InvokeInterface $invoke
     * @param ContainerInterface $container
     * @return void
     */
    public function unload(InvokeInterface $invoke, ContainerInterface $container): void;
}