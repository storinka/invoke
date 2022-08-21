<?php

namespace Invoke\Support;

use Invoke\Container;
use Invoke\NewMethod\Description\MethodDescriptionInterface;
use Invoke\NewMethod\Information\PipeProxyInterface;
use Invoke\NewMethod\MethodInterface;
use Invoke\Piping;

/**
 * Method proxy that allows lazy initialization of a method.
 */
class MethodClassProxy implements MethodInterface, PipeProxyInterface
{
    /**
     * Lazy initialized method instance.
     *
     * @var MethodInterface $methodInstance
     */
    private MethodInterface $methodInstance;

    /**
     * Method class proxy constructor.
     *
     * @param class-string<MethodInterface> $methodClass
     */
    public function __construct(private readonly string $methodClass)
    {
    }

    /**
     * @inheritDoc
     */
    public function asInvokeGetParametersInformation(): array
    {
        return $this->getMethodInstance()->asInvokeGetParametersInformation();
    }

    /**
     * @inheritDoc
     */
    public function asInvokeGetMethodDescription(): MethodDescriptionInterface
    {
        return $this->getMethodInstance()->asInvokeGetMethodDescription();
    }

    /**
     * @inheritDoc
     */
    public function run(mixed $value): mixed
    {
        return $this->getMethodInstance()->run($value);
    }

    /**
     * Get instance of the method.
     */
    private function getMethodInstance(): MethodInterface
    {
        if (!isset($this->methodInstance)) {
            $this->methodInstance = Container::make($this->methodClass);
        }

        return $this->methodInstance;
    }

    public static function invoke(array $input = []): mixed
    {
        return Piping::run(static::class, $input);
    }
}