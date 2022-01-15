<?php

namespace InvokeTests\Typesystem;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Types;
use Invoke\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    use SetupInvoke;

    public function testStringShouldNotFail()
    {
        $result = Typesystem::validateParam("some_name", Types::string, "some name");
        $this->assertEquals("some name", $result);
    }

    public function testStringAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::string, null);
    }

    public function testStringAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::string, 10);
    }

    public function testStringAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::string, true);
    }

    public function testStringAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::string, []);
    }
}
