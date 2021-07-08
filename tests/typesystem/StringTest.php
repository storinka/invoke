<?php

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
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

    public function testBoolAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, 10);
    }

    public function testStringAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, true);
    }

    public function testBoolAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_name", Types::String, []);
    }
}
