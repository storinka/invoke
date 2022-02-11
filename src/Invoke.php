<?php

namespace Invoke;

use Invoke\Meta\MethodExtension;
use Invoke\Pipelines\DefaultErrorPipeline;
use Invoke\Pipelines\DefaultPipeline;
use Invoke\Pipes\FunctionPipe;
use Invoke\Utils\Utils;
use Throwable;

/**
 * Invoke pipe itself.
 *
 * @final
 */
class Invoke implements InvokeInterface
{
    /**
     * Invoke version.
     *
     * @var string $version
     */
    public static string $version = "2.0.0-ALPHA";

    /**
     * Method extensions.
     *
     * @var MethodExtension[] $methodExtensions
     */
    protected array $methodExtensions = [
    ];

    /**
     * Methods map.
     *
     * @var array $methods
     */
    protected array $methods = [
    ];

    /**
     * Invoke configuration.
     *
     * @var array $config
     */
    protected array $config = [
        "server" => [
            "pathPrefix" => "/"
        ],
        "inputMode" => [
            "convertStrings" => true,
        ],
        "types" => [
            "alwaysRequireName" => false,
            "alwaysReturnName" => false,
        ],
        "serve" => [
            "defaultPipeline" => DefaultPipeline::class,
            "defaultErrorPipeline" => DefaultErrorPipeline::class,
        ]
    ];

    /**
     * Is input mode enabled.
     *
     * @var bool $inputMode
     */
    public bool $inputMode = false;

    /**
     * Pipe filter function.
     *
     * @param mixed $value
     * @return mixed
     */
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $name = $value["name"];
        $params = $value["params"];

        return $this->invoke(
            $name,
            $params
        );
    }

    /**
     * @inheritDoc
     */
    public function invoke(string $name, array $params = []): mixed
    {
        $method = $this->methods[$name];

        if (is_callable($method)) {
            $method = new FunctionPipe($method);
        }

        return Pipeline::pass($method, $params);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(string $property): mixed
    {
        $path = explode(".", $property);

        $value = $this->config;

        foreach ($path as $key) {
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setMethods(array $methods): static
    {
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

        $this->methods = $methods;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function getMethod(string $name): string|callable|null
    {
        if ($this->hasMethod($name)) {
            return $this->methods[$name];
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasMethod(string $name): bool
    {
        return in_array($name, array_keys($this->getMethods()));
    }

    /**
     * @inheritDoc
     */
    public function deleteMethod(string $name): void
    {
        $newMethods = [];

        foreach ($this->methods as $methodName => $method) {
            if ($methodName === $name) {
                continue;
            }

            $newMethods[$methodName] = $method;
        }

        $this->methods = $newMethods;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): static
    {
        $this->config = array_merge_recursive2($this->config, $config);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isInputMode(): bool
    {
        return $this->inputMode;
    }

    /**
     * @inheritDoc
     */
    public function setInputMode(bool $inputMode): void
    {
        $this->inputMode = $inputMode;
    }

    /**
     * @inheritDoc
     */
    public function registerMethodExtension(string $extensionClass, array $parameters = []): mixed
    {
        $extension = Container::make($extensionClass, $parameters);

        $this->methodExtensions[] = $extension;

        return $extension;
    }

    /**
     * @inheritDoc
     */
    public function getMethodExtensions(): array
    {
        return $this->methodExtensions;
    }

    /**
     * @inheritDoc
     */
    public function serve(array|Pipe|string|null $pipeline = null,
                          mixed                  $input = null): mixed
    {
        if (!$pipeline) {
            $pipeline = $this->getConfig("serve.defaultPipeline");
        }

        try {
            return Pipeline::pass($pipeline, $input);
        } catch (Throwable $exception) {
            $errorPipeline = $this->getConfig("serve.defaultErrorPipeline");

            return Pipeline::pass($errorPipeline, $exception);
        }
    }

    /**
     * Create new instance of Invoke.
     *
     * @param array $methods
     * @param array $config
     * @param bool $setContainer
     * @return static
     */
    public static function create(array $methods = [],
                                  array $config = [],
                                  bool  $setContainer = true): static
    {
        $invoke = new Invoke();

        $invoke->setMethods($methods);
        $invoke->setConfig($config);

        if ($setContainer) {
            Container::singleton(Invoke::class, $invoke);
        }

        return $invoke;
    }
}