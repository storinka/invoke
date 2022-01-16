<?php

namespace Invoke;

use Closure;

abstract class MethodExtension
{
    public function init(Method|string|Closure $method): void
    {
    }

    public function beforeHandle(Method|string|Closure $method, array $params = []): void
    {
    }

    public function afterHandle(Method|string|Closure $method, mixed $result): void
    {
    }
}