<?php

namespace Invoke;

use Attribute;

#[Attribute]
abstract class Validation extends AbstractPipe
{
    public Pipe $parentPipe;
    public string $paramName;

    public function getValidationData(): array
    {
        return [];
    }
}