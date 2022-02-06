<?php

namespace Invoke\Types;

use Invoke\Pipe;
use Invoke\Type;

class Optional extends Type
{
    public function __construct(
        public Pipe  $pipe,
        public mixed $defaultValue
    )
    {
    }

    public function pass(mixed $value): mixed
    {
        return $value;
    }
}