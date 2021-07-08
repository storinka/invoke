<?php

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use PHPUnit\Framework\TestCase;

class FloatTest extends TestCase
{
    public function testFloatShouldNotFail()
    {
        $result = Typesystem::validateParam("some_price", Types::Float, 10);
        $this->assertEquals(10, $result);

        $result = Typesystem::validateParam("some_price", Types::Float, 1.5);
        $this->assertEquals(1.5, $result);
    }

    public function testFloatAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::Float, null);
    }

    public function testFloatAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::Float, true);
    }

    public function testFloatAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::Float, "a string");
    }

    public function testFloatAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::Float, []);
    }
}
