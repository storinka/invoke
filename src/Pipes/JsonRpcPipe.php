<?php

namespace Invoke\Pipes;

use Invoke\Pipe;
use Invoke\Stop;

class JsonRpcPipe implements Pipe
{
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        // TODO: Implement pass() method.
    }
}