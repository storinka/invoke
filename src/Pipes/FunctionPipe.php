<?php

namespace Invoke\Pipes;

use Closure;
use Invoke\AbstractPipe;

class FunctionPipe extends AbstractPipe
{
    public Closure|string $function;

    public function __construct(Closure|string $function)
    {
        $this->function = $function;
    }

    public function pass(mixed $value): mixed
    {
        return call_user_func_array(
            $this->function,
            $value
        );
    }
}