<?php

namespace Invoke;

use Invoke\Meta\MethodExtension;

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
     * @return mixed
     */
    public function getConfig(string $property): mixed;

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
     * Get method.
     *
     * @param string $name
     * @return string|callable|null
     */
    public function getMethod(string $name): string|callable|null;

    /**
     * Check if method exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasMethod(string $name): bool;

    /**
     * Delete method.
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
     * @template T of \Invoke\Meta\MethodExtension
     *
     * @param class-string<T> $extensionClass
     * @param array $parameters
     * @return T
     */
    public function registerMethodExtension(string $extensionClass, array $parameters = []): mixed;

    /**
     * @return MethodExtension[]
     */
    public function getMethodExtensions(): array;

    /**
     * Run main pipeline.
     *
     * @param array|Pipe|string|null $pipeline
     * @param mixed|null $input
     * @return mixed
     */
    public function serve(array|Pipe|string|null $pipeline = null, mixed $input = null): mixed;
}