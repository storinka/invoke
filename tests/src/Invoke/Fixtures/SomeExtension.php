<?php

namespace InvokeTests\Invoke\Fixtures;

use Invoke\Extensions\Extension;
use Invoke\Invoke;
use Psr\Container\ContainerInterface;

class SomeExtension implements Extension
{
    public function load(Invoke $invoke, ContainerInterface $container): void
    {
        throw new \RuntimeException('called SomeExtension::boot');
    }
}