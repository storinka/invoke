<?php

namespace Invoke\Abstractions\Description;

use Invoke\NewMethod\ParameterInformationInterface;

/**
 * Abstract method interface.
 */
interface MethodDescriptionInterface
{
    /**
     * @return array<string, ParameterInformationInterface>
     */
    public function getParameterTypesDescription(): array;

    public function getResultTypeDescription(): TypeDescriptionInterface;
}