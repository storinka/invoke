<?php

namespace Invoke\Pipes;

use Closure;
use Invoke\Pipe;
use Invoke\Stop;

class FunctionPipe implements Pipe
{
    public Closure|string $function;

    public function __construct(Closure|string $function)
    {
        $this->function = $function;
    }

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        return call_user_func_array(
            $this->function,
            $value
        );
    }
}
