<?php

namespace Invoke;

use Closure;

abstract class Extension
{
    public function registered()
    {

    }

    public function unregistered()
    {

    }

    public function methodInit(Method|string|Closure $method)
    {
    }

    public function methodBeforeHandle(Method|string|Closure $method, array $params = [])
    {
    }

    public function methodAfterHandle(Method|string|Closure $method, mixed $result)
    {
    }
}