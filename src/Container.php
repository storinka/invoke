<?php

namespace Invoke;

use Invoke\Container\InvokeContainer;
use Invoke\Container\InvokeContainerInterface;

/**
 * Container facade.
 *
 * @template G
 *
 * @method static bool has(string $id)
 * @method static void factory(string $id, callable|string|null $factory = null)
 * @method static void singleton(string $id, callable|object|string|null $singleton = null)
 * @method static void delete(string $id)
 * @method static mixed make(callable|string $idOrCallable, array $parameters = [])
 */
final class Container
{
    protected static InvokeContainerInterface $instance;

    public static function setCurrent(InvokeContainerInterface $container): void
    {
        Container::$instance = $container;
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

    public static function __callStatic(string $name, array $arguments)
    {
        return Container::current()->{$name}(...$arguments);
    }
}