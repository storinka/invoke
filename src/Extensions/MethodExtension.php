<?php

namespace Invoke\Extensions;

use Invoke\Invoke;
use Invoke\Method;
use Psr\Container\ContainerInterface;

/**
 * Method extension abstract class.
 */
abstract class MethodExtension implements Extension
{
    /**
     * @inheritDoc
     */
    public function boot(Invoke $invoke, ContainerInterface $container): void
    {
    }

    /**
     * This hook is called before method parameters validation and before "beforeHandle" hook.
     *
     * @param Method $method
     * @return void
     */
    public function beforeValidation(Method $method): void
    {
    }

    /**
     * This hook is called after "beforeValidation" and before "handle".
     *
     * @param Method $method
     * @return void
     */
    public function beforeHandle(Method $method): void
    {
    }

    /**
     * This hook called after "handle" and before result is returned.
     *
     * @param Method $method
     * @param mixed $result
     * @return void
     */
    public function afterHandle(Method $method, mixed $result): void
    {
    }
}
