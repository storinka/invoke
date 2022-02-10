<?php

namespace Invoke\Meta;

use Invoke\Method;

interface MethodExtension
{
    public function beforeHandle(Method $method): void;

    public function afterHandle(Method $method, mixed $result): void;
}