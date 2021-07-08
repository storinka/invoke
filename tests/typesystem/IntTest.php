<?php

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use PHPUnit\Framework\TestCase;

class IntTest extends TestCase
{
    public function testIntShouldNotFail()
    {
        $result = Typesystem::validateParam("some_id", Types::Int, 10);
        $this->assertEquals(10, $result);

        $result = Typesystem::validateParam("some_id", Types::Int, 1.5);
        $this->assertEquals(1.0, $result);
    }

    public function testIntAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::Int, null);
    }

    public function testIntAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::Int, true);
    }

    public function testIntAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::Int, "a string");
    }

    public function testIntAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::Int, []);
    }
}
