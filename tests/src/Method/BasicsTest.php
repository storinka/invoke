<?php

namespace InvokeTests\Method;

use InvokeTests\Method\Fixtures\SomeMethod;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;

class BasicsTest extends TestCase
{
    public function test()
    {
        $result = SomeMethod::invoke([
            "paramAsProperty" => 123,
            "paramAsArg" => 456
        ]);

        assertEquals("123456", $result);
    }
}