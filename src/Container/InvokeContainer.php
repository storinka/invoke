<?php

namespace Invoke\Container;

use InvalidArgumentException;
use Invoke\Utils\ReflectionUtils;
use ReflectionFunction;
use function class_exists;
use function is_callable;
use function is_string;

/**
 * Default invoke container implementation.
 */
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

            if (is_object($singleton) && !is_callable($singleton)) {
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
        return array_key_exists($id, $this->factories)
            || array_key_exists($id, $this->singletons);
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
    public function make(callable|string $classOrCallable, array $parameters = []): mixed
    {
        if (is_string($classOrCallable) && class_exists($classOrCallable)) {
            $classOrCallable = $this->factories[$classOrCallable] ?? $classOrCallable;
        }

        if (is_callable($classOrCallable)) {
            return $this->resolveFunction($classOrCallable, $parameters);
        }

        if (is_string($classOrCallable) && class_exists($classOrCallable)) {
            return $this->resolveClass($classOrCallable, $parameters);
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
                    $type = $reflectionProperty->getType()->getName();

                    $value = $this->get($type);

                    $reflectionProperty->setValue($instance, $value);
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
