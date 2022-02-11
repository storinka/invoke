<?php

namespace Invoke;

use Invoke\Container\InvokeContainer;
use Invoke\Container\InvokeContainerInterface;
use Psr\Container\ContainerInterface;

/**
 * Container facade.
 *
 * @method static bool has(string $id)
 * @method static void factory(string $id, callable|string|null $factory = null)
 * @method static void singleton(string $id, callable|object|string|null $singleton = null)
 * @method static void delete(string $id)
 */
final class Container
{
    protected static InvokeContainerInterface $instance;

    public static function setCurrent(InvokeContainerInterface $container): void
    {
        Container::$instance = $container;

        Container::singleton(ContainerInterface::class, $container);
        Container::singleton(InvokeContainerInterface::class, $container);
    }

    public static function current(): InvokeContainerInterface
    {
        if (empty(Container::$instance)) {
            Container::setCurrent(new InvokeContainer());
        }

        return Container::$instance;
    }

    /**
     * @template T
     *
     * @param class-string<T> $id
     * @return T|null
     */
    public static function get(string $id): mixed
    {
        return Container::current()->get($id);
    }

    /**
     * @template T
     *
     * @param callable|class-string<T> $classOrCallable
     * @param array $parameters
     * @return mixed|T
     */
    public static function make(callable|string $classOrCallable, array $parameters = []): mixed
    {
        return Container::current()->make($classOrCallable, $parameters);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return Container::current()->{$name}(...$arguments);
    }
}