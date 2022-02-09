<?php

namespace Invoke\Container;

use InvalidArgumentException;
use Invoke\Support\Singleton;
use ReflectionClass;
use ReflectionFunction;

class Container implements Singleton, InvokeContainerInterface
{
    protected static InvokeContainerInterface $instance;

    protected array $factories = [];
    protected array $singletons = [];

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        if (isset($this->factories[$id])) {
            $factory = $this->factories[$id];

            return $this->make($factory);
        }

        if (isset($this->singletons[$id])) {
            $singleton = $this->singletons[$id];

            if (is_object($singleton)) {
                return $singleton;
            }

            return $this->singletons[$id] = $this->make($singleton);
        }

        throw new InvokeContainerNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return in_array($id, $this->factories)
            || in_array($id, $this->singletons);
    }

    /**
     * @inheritDoc
     */
    public function factory(string $id, callable|string|null $factory = null)
    {
        $this->factories[$id] = $factory;
    }

    /**
     * @inheritDoc
     */
    public function singleton(string $id, callable|object|string|null $singleton = null)
    {
        $this->singletons[$id] = $singleton ?? $id;

        if (is_object($singleton) && $id !== $singleton::class) {
            $this->singleton($singleton::class, $singleton);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $id): void
    {
        if (isset($this->factories[$id])) {
            unset($this->factories[$id]);
        }

        if (isset($this->singletons[$id])) {
            unset($this->singletons[$id]);
        }
    }

    /**
     * @inheritDoc
     */
    public function make(callable|string $idOrCallable, array $parameters = []): mixed
    {
        if (is_callable($idOrCallable)) {
            return $this->resolveFunction($idOrCallable, $parameters);
        }

        if (is_string($idOrCallable) && class_exists($idOrCallable)) {
            return $this->resolveClass($idOrCallable, $parameters);
        }

        return null;
    }

    public function resolveMethod(object $object, string $method)
    {
        $reflectionClass = new ReflectionClass($object);
        $reflectionMethod = $reflectionClass->getMethod($method);

        $params = $this->resolveParameters($reflectionMethod->getParameters());

        return $object->{$method}(...$params);
    }

    public function resolveStaticMethod(string $class, string $method)
    {
        $reflectionClass = new ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getMethod($method);

        $params = $this->resolveParameters($reflectionMethod->getParameters());

        return $class::{$method}(...$params);
    }

    public function resolveClass(string $class, array $parameters = [])
    {
        $reflectionClass = new ReflectionClass($class);

        if ($reflectionClass->isInstantiable()) {
            $reflectionConstructor = $reflectionClass->getConstructor();

            if ($reflectionConstructor) {
                $params = $this->resolveParameters($reflectionConstructor->getParameters(), $parameters);
            } else {
                $params = [];
            }

            return new $class(...$params);
        }

        throw new InvalidArgumentException("The class is not instantiable.");
    }

    public function resolveFunction($function, array $parameters = [])
    {
        $reflectionFunction = new ReflectionFunction($function);
        $params = $this->resolveParameters($reflectionFunction->getParameters(), $parameters);

        return $function(...$params);
    }

    /**
     * @param array $parameters
     * @param array $customParameters
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function resolveParameters(array $parameters, array $customParameters = []): array
    {
        $params = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            if (array_key_exists($paramName, $customParameters)) {
                $params[] = $customParameters[$paramName];
                continue;
            }

            $paramType = $parameter->getType();

            if ($paramType->isBuiltin()) {
                $params[] = null;
            } else {
                $params[] = $this->get($paramType->getName());
            }
        }

        return $params;
    }

    public static function setInstance(Container $container): void
    {
        static::$instance = $container;
    }

    public static function getInstance(): Container
    {
        if (empty(static::$instance)) {
            static::setInstance(new Container());
        }

        return static::$instance;
    }
}