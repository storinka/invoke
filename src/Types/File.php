<?php

namespace Invoke\Types;

use Invoke\Type;

class File extends Type
{
    public function pass(mixed $value): mixed
    {
        return $this;
    }
}