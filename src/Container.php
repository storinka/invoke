<?php

namespace Invoke;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionFunction;

class Container implements Singleton
{
    protected static Container $instance;

    protected array $bindings = [];
    protected array $singletons = [];

    public function bind(string $abstract, $service)
    {
        $this->bindings[$abstract] = $service;
    }

    public function singleton(string $abstract, $service)
    {
        $this->singletons[$abstract] = $service;
    }

    public function get(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            return $this->resolve($this->bindings[$abstract]);
        }

        if (isset($this->singletons[$abstract])) {
            $service = $this->singletons[$abstract];

            if (is_callable($service) || (is_string($service) && class_exists($service))) {
                $service = $this->resolve($service);

                $this->singletons[$abstract] = $service;
            }

            return $service;
        }

        return $this->resolve($abstract);
    }

    public function delete(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            unset($this->bindings[$abstract]);
        }

        if (isset($this->singletons[$abstract])) {
            unset($this->singletons[$abstract]);
        }
    }

    public function resolve($classOrFunction)
    {
        if (is_callable($classOrFunction)) {
            return $this->resolveFunction($classOrFunction);
        }

        if (is_string($classOrFunction) && function_exists($classOrFunction)) {
            return $this->resolveFunction($classOrFunction);
        }

        if (is_string($classOrFunction) && class_exists($classOrFunction)) {
            return $this->resolveClass($classOrFunction);
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

    public function resolveClass(string $class)
    {
        $reflectionClass = new ReflectionClass($class);

        if ($reflectionClass->isInstantiable()) {
            $reflectionConstructor = $reflectionClass->getConstructor();

            if ($reflectionConstructor) {
                $params = $this->resolveParameters($reflectionConstructor->getParameters());
            } else {
                $params = [];
            }

            return new $class(...$params);
        }

        throw new InvalidArgumentException("The class is not instantiable.");
    }

    public function resolveFunction($function)
    {
        $reflectionFunction = new ReflectionFunction($function);
        $params = $this->resolveParameters($reflectionFunction->getParameters());

        return $function(...$params);
    }

    protected function resolveParameters(array $parameters): array
    {
        $params = [];

        foreach ($parameters as $param) {
            $paramType = $param->getType();

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