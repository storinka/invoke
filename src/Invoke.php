<?php

namespace Invoke;

use Invoke\Container\InvokeContainerInterface;
use Invoke\Meta\MethodExtension;
use Invoke\Pipelines\DefaultErrorPipeline;
use Invoke\Pipelines\DefaultPipeline;
use Invoke\Pipes\FunctionPipe;
use Invoke\Support\Singleton;
use Invoke\Utils\Utils;
use Psr\Container\ContainerInterface;
use Throwable;

use function Invoke\Utils\array_merge_recursive2;

/**
 * Invoke pipe itself.
 */
final class Invoke implements Pipe, Singleton
{
    public static string $libraryVersion = "2.0.0-ALPHA";

    protected static Invoke $instance;

    protected static array $methodExtensions = [
    ];
    protected array $methods = [
    ];
    protected array $config = [
        "server" => [
            "pathPrefix" => "/"
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

    public static function hasMethod(string $name): bool
    {
        return in_array($name, array_keys(static::getMethods()));
    }

    public static function setup(array $methods = [], array $config = [])
    {
        $invoke = Invoke::getInstance();

        Container::singleton(Invoke::class, $invoke);
        Container::singleton(ContainerInterface::class, Container::current());
        Container::singleton(InvokeContainerInterface::class, Container::current());

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

        $invoke->methods = $methods;
        $invoke->config = array_merge_recursive2($invoke->config, $config);
    }

    public static function serve($pipeline = null, mixed $params = null)
    {
        if (!$pipeline) {
            $pipeline = DefaultPipeline::class;
        }

        try {
            return Pipeline::pass($pipeline, $params);
        } catch (Throwable $exception) {
            return Pipeline::pass(DefaultErrorPipeline::class, $exception);
        }
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
            static::$instance = Container::make(static::class);
        }

        return static::$instance;
    }

    /**
     * @template T of \Invoke\Meta\MethodExtension
     *
     * @param class-string<T> $extensionClass
     * @param array $parameters
     * @return T
     */
    public static function registerMethodExtension(string $extensionClass, array $parameters = []): mixed
    {
        $extension = Container::make($extensionClass, $parameters);

        Invoke::$methodExtensions[] = $extension;

        return $extension;
    }

    /**
     * @return MethodExtension[]
     */
    public static function getMethodExtensions(): array
    {
        return Invoke::$methodExtensions;
    }
}
