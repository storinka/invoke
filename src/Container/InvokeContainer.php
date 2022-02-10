<?php

namespace Invoke\Container;

use InvalidArgumentException;
use Invoke\Container;
use Invoke\Utils\ReflectionUtils;
use ReflectionFunction;

class InvokeContainer implements InvokeContainerInterface
{
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
    public function factory(string $id, callable|string|null $factory = null): void
    {
        $this->factories[$id] = $factory;
    }

    /**
     * @inheritDoc
     */
    public function singleton(string $id, callable|object|string|null $singleton = null): void
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

    protected function resolveMethod(object $object, string $method)
    {
        $reflectionClass = ReflectionUtils::getClass($object);
        $reflectionMethod = $reflectionClass->getMethod($method);

        $params = $this->resolveParameters($reflectionMethod->getParameters());

        return $object->{$method}(...$params);
    }

    protected function resolveStaticMethod(string $class, string $method)
    {
        $reflectionClass = ReflectionUtils::getClass($class);
        $reflectionMethod = $reflectionClass->getMethod($method);

        $params = $this->resolveParameters($reflectionMethod->getParameters());

        return $class::{$method}(...$params);
    }

    protected function resolveClass(string $class, array $parameters = [])
    {
        $reflectionClass = ReflectionUtils::getClass($class);

        if ($reflectionClass->isInstantiable()) {
            $reflectionConstructor = $reflectionClass->getConstructor();

            if ($reflectionConstructor) {
                $params = $this->resolveParameters($reflectionConstructor->getParameters(), $parameters);
            } else {
                $params = [];
            }

            $instance = new $class(...$params);

            foreach ($reflectionClass->getProperties() as $reflectionProperty) {
                if (ReflectionUtils::isPropertyDependency($reflectionProperty)) {
                    $name = $reflectionProperty->getName();
                    $type = $reflectionProperty->getType()->getName();

                    $value = Container::get($type);

                    $this->{$name} = $value;
                }
            }

            return $instance;
        }

        throw new InvalidArgumentException("The class is not instantiable.");
    }

    protected function resolveFunction($function, array $parameters = [])
    {
        $reflectionFunction = new ReflectionFunction($function);
        $params = $this->resolveParameters($reflectionFunction->getParameters(), $parameters);

        return $function(...$params);
    }

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
}