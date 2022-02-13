<?php

namespace Invoke\Meta;

use Invoke\Extensions\MethodExtension;
use Invoke\Invoke;
use Invoke\Method;
use Psr\Container\ContainerInterface;

abstract class AbstractMethodExtension implements MethodExtension
{
    public function boot(Invoke $invoke, ContainerInterface $container): void
    {
        // TODO: Implement boot() method.
    }

    public function beforeValidateParams(Method $method): void
    {
        // TODO: Implement beforeHandle() method.
    }

    public function beforeHandle(Method $method): void
    {
        // TODO: Implement beforeHandle() method.
    }

    public function afterHandle(Method $method, mixed $result): void
    {
        // TODO: Implement afterHandle() method.
    }
}