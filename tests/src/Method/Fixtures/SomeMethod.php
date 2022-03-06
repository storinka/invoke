<?php

namespace InvokeTests\Method\Fixtures;

use Invoke\Method;

class SomeMethod extends Method
{
    public int $paramAsProperty;

    protected function handle(int $paramAsArg): string
    {
        return $this->paramAsProperty . $paramAsArg;
    }
}