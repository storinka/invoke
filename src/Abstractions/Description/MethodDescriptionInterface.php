<?php

namespace Invoke\Abstractions\Description;

/**
 * Abstract method interface.
 */
interface MethodDescriptionInterface
{
    /**
     * @return array<string, ParameterDescriptionInterface>
     */
    public function getParameterTypesDescription(): array;

    public function getResultTypeDescription(): TypeDescriptionInterface;
}