<?php

namespace Invoke;

use Invoke\Extensions\Extension;
use Invoke\Extensions\MethodExtension;
use Invoke\NewMethod\MethodInterface;

interface InvokeInterface extends Pipe
{
    /**
     * Invoke a method.
     *
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function invoke(string $name, array $params = []): mixed;

    /**
     * Get configuration property value.
     *
     * @param string $property
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function getConfig(string $property, mixed $defaultValue = null): mixed;

    /**
     * Set methods.
     *
     * @param array $methods
     * @return $this
     */
    public function setMethods(array $methods): static;

    /**
     * Get available methods.
     *
     * @return array
     */
    public function getMethods(): array;

    /**
     * Get a method.
     *
     * @param string $name
     * @return class-string|callable|null
     */
    public function getMethod(string $name): MethodInterface|null;

    /**
     * Set a method.
     *
     * @param string $name
     * @param class-string|callable $method
     * @return InvokeInterface
     */
    public function setMethod(string $name, string|callable|MethodInterface $method): static;

    /**
     * Check if method exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasMethod(string $name): bool;

    /**
     * Delete a method.
     *
     * @param string $name
     * @return void
     */
    public function deleteMethod(string $name): void;

    /**
     * Set config.
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config): static;

    /**
     * Check is input mode enabled.
     *
     * @return bool
     */
    public function isInputMode(): bool;

    /**
     * Set input mode status.
     *
     * @param bool $inputMode
     * @return void
     */
    public function setInputMode(bool $inputMode): void;

    /**
     * @template T of \Invoke\Meta\Extension
     *
     * @param class-string<T> $extensionClass
     * @param array $parameters
     * @return static
     */
    public function registerExtension(string $extensionClass, array $parameters = []): static;

    /**
     * @return Extension[]|MethodExtension[]
     */
    public function getExtensions(): array;

    /**
     * Run main pipeline.
     *
     * @param array|Pipe|class-string|null $pipeline
     * @param mixed|null $input
     * @return mixed
     */
    public function run(array|Pipe|string|null $pipeline = null, mixed $input = null): mixed;

    /**
     * Boot registered extensions.
     *
     * @return void
     */
    public function bootExtensions(): void;
}