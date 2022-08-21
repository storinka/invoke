<?php

namespace InvokeTests\Method\Fixtures;

use Invoke\Method;

class SomeMethod extends Method
{
    protected function handle(int $paramAsProperty, int $paramAsArg): string
    {
        return $paramAsProperty . $paramAsArg;
    }
}