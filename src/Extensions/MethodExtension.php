<?php

namespace Invoke\Extensions;

use Invoke\InvokeInterface;
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
    public function load(InvokeInterface $invoke, ContainerInterface $container): void
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
     * @param array $parameters
     * @return void
     */
    public function beforeHandle(Method $method, array $parameters): void
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
