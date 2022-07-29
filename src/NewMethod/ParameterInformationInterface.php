<?php

namespace Invoke\NewMethod;

use Invoke\Pipe;

interface ParameterInformationInterface
{
    public function getName(): string;

    public function getPipe(): string|Pipe;

    public function hasDefaultValue(): bool;

    public function getDefaultValue(): mixed;

    public function isRequired(): bool;

    public function isNullable(): bool;

    public function getValidators(): array;
}