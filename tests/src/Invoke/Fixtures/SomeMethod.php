<?php

namespace InvokeTests\Invoke\Fixtures;

use Invoke\Method;

class SomeMethod extends Method
{
    protected function handle(int $param = 0): int
    {
        return 123 + $param;
    }
}