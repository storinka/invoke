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

//    public function testToArray()
//    {
//        $input = [
//            "paramAsProperty" => 123,
//            "paramAsArg" => 456
//        ];
//
//        $method = new SomeMethod();
//        $method->pass($input);
//
//        assertEquals($input, $method->toArray());
//    }
}