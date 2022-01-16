<?php

namespace Invoke;

use Closure;

abstract class Extension
{
    public function registered(): void
    {
    }

    public function unregistered(): void
    {
    }

    public function methodInit(Method|string|Closure $method): void
    {
    }

    public function methodBeforeHandle(Method|string|Closure $method, array $params = []): void
    {
    }

    public function methodAfterHandle(Method|string|Closure $method, mixed $result): void
    {
    }
}