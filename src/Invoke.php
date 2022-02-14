<?php

namespace Invoke;

use Invoke\Exceptions\MethodNotFoundException;
use Invoke\Extensions\Extension;
use Invoke\Extensions\MethodExtension;
use Invoke\Pipelines\MainPipeline;
use Invoke\Support\FunctionPipe;
use function Invoke\Utils\array_merge_recursive2;
use function Invoke\Utils\prepare_methods;

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
     * @var Extension[]|MethodExtension[] $extensions
     */
    protected array $extensions = [
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
            "defaultPipeline" => MainPipeline::class,
        ],
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
        if (!$this->hasMethod($name)) {
            throw new MethodNotFoundException($name);
        }

        $method = $this->methods[$name];

        if (is_callable($method)) {
            $method = new FunctionPipe($method);
        }

        return Piping::run($method, $params);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(string $property, mixed $defaultValue = null): mixed
    {
        $path = explode(".", $property);

        $value = $this->config;

        foreach ($path as $key) {
            if (is_array($value)) {
                if (array_key_exists($key, $value)) {
                    $value = $value[$key];
                } else {
                    return $defaultValue;
                }
            } else {
                break;
            }
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setMethods(array $methods): static
    {
        $this->methods = prepare_methods($methods);

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
    public function setMethod(string $name, callable|string $method): static
    {
        $this->methods[$name] = $method;

        return $this;
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
    public function registerExtension(string $extensionClass, array $parameters = []): static
    {
        /** @var Extension $extension */
        $extension = Container::make($extensionClass, $parameters);

        $this->extensions[] = $extension;

        $extension->boot($this, Container::current());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getExtensions(): array
    {
        return $this->extensions;
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

        return Piping::run($pipeline, $input);
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
