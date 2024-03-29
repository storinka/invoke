<?php

namespace InvokeTests\TypeWithParams\Fixtures\Pipes;

use Attribute;
use Invoke\Pipe;

#[Attribute]
class DoubleValuePipe implements Pipe
{
    public function pass(mixed $value): int|float
    {
        return $value * 2;
    }
}