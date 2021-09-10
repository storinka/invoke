<?php

namespace InvokeTests\Typesystem;

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    use SetupInvoke;

    public function testStringShouldNotFail()
    {
        $result = Typesystem::validateParam("some_name", Types::String, "some name");
        $this->assertEquals("some name", $result);
    }

    public function testStringAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, null);
    }

    public function testStringAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, 10);
    }

    public function testStringAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, true);
    }

    public function testStringAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, []);
    }
}
