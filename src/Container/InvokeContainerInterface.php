<?php

namespace Invoke\Container;

use Psr\Container\ContainerInterface;

interface InvokeContainerInterface extends ContainerInterface
{
    /**
     * Register factory.
     *
     * @param string $id
     * @param callable|class-string|null $factory
     * @return void
     */
    public function factory(string $id, callable|string|null $factory = null);

    /**
     * @param string $id
     * @param callable|object|string|null $singleton
     * @return void
     */
    public function singleton(string $id, callable|object|string|null $singleton = null);

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id): void;

    /**
     * @param callable|class-string $idOrCallable
     * @param array $parameters
     * @return mixed
     */
    public function make(callable|string $idOrCallable, array $parameters = []): mixed;
}