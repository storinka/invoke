<?php

namespace Invoke;

use Invoke\Container\Container;
use Invoke\Container\InvokeContainerInterface;
use Invoke\Pipes\FunctionPipe;
use Invoke\Pipes\HttpPipe;
use Invoke\Support\Singleton;
use Invoke\Utils\Utils;
use Psr\Container\ContainerInterface;

/**
 * Invoke pipe itself.
 */
class Invoke implements Pipe, Singleton
{
    protected static Invoke $instance;

    protected array $methods = [
    ];
    protected array $config = [
        "server" => [
            "pathPrefix" => "invoke"
        ],
        "inputMode" => [
            "convertStrings" => true,
        ],
    ];
    public bool $inputMode = false;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $name = $value["name"];
        $params = $value["params"];

        return static::invoke(
            $name,
            $params
        );
    }

    public static function config(string $property): mixed
    {
        $path = explode(".", $property);

        $value = static::getInstance()->config;

        foreach ($path as $key) {
            $value = $value[$key];
        }

        return $value;
    }

    public static function invoke(string $name, array $params = [])
    {
        $method = static::getInstance()->methods[$name];

        if (is_callable($method)) {
            $method = new FunctionPipe($method);
        }

        return Pipeline::pass($method, $params);
    }

    public static function setup(array $methods = [])
    {
        $container = Container::getInstance();

        $container->singleton(Invoke::class, Invoke::getInstance());
        $container->singleton(ContainerInterface::class, $container);
        $container->singleton(InvokeContainerInterface::class, $container);

        foreach ($methods as $name => $method) {
            if (is_numeric($name) && is_string($method)) {
                unset($methods[$name]);

                if (class_exists($method)) {
                    $methods[Utils::getMethodNameFromClass($method)] = $method;
                } else {
                    $methods[$method] = $method;
                }
            }
        }

        static::getInstance()->methods = $methods;
    }

    public static function serve($modeOrPipe = HttpPipe::class, mixed $params = null)
    {
        return Pipeline::pass($modeOrPipe, $params);
    }

    public static function isInputMode(): bool
    {
        return static::getInstance()->inputMode;
    }

    public static function setInputMode(bool $inputMode): void
    {
        static::getInstance()->inputMode = $inputMode;
    }

    public static function getMethods(): array
    {
        return static::getInstance()->methods;
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = Container::getInstance()->make(static::class);
        }

        return static::$instance;
    }
}