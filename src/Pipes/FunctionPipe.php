<?php

namespace Invoke\Pipes;

use Closure;
use Invoke\Container\Container;
use Invoke\Pipe;

class FunctionPipe implements Pipe
{
    public Closure|string $function;

    public function __construct(Closure|string $function)
    {
        $this->function = $function;
    }

    public function pass(mixed $value): mixed
    {
        return Container::getInstance()->resolveFunction([$this->function], $value);
    }
}