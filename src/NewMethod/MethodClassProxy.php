<?php

namespace Invoke\NewMethod;

use Invoke\Container;

/**
 * Method proxy that allows lazy initialization of a method.
 */
class MethodClassProxy implements MethodInterface
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
    public function getParametersInformation(): array
    {
        return $this->getMethodInstance()->getParametersInformation();
    }

    /**
     * @inheritDoc
     */
    public function pass(mixed $value): mixed
    {
        return $this->getMethodInstance()->pass($value);
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
}