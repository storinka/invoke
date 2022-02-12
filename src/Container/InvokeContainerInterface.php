<?php

namespace Invoke\Container;

use Psr\Container\ContainerInterface;

/**
 * Invoke container interface.
 */
interface InvokeContainerInterface extends ContainerInterface
{
    /**
     * Register factory.
     *
     * @param string $id
     * @param callable|class-string|null $factory
     * @return void
     */
    public function factory(string $id, callable|string|null $factory = null): void;

    /**
     * @param string $id
     * @param callable|object|string|null $singleton
     * @return void
     */
    public function singleton(string $id, callable|object|string|null $singleton = null): void;

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void;

    /**
     * @template T
     *
     * @param callable|class-string<T> $classOrCallable
     * @param array $parameters
     * @return mixed|T
     */
    public function make(callable|string $classOrCallable, array $parameters = []): mixed;
}
