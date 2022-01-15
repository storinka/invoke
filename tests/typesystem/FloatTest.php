<?php

namespace InvokeTests\Typesystem;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Types;
use Invoke\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class FloatTest extends TestCase
{
    use SetupInvoke;

    public function testFloatShouldNotFail()
    {
        $result = Typesystem::validateParam("some_price", Types::float, 10);
        $this->assertEquals(10, $result);

        $result = Typesystem::validateParam("some_price", Types::float, 1.5);
        $this->assertEquals(1.5, $result);
    }

    public function testFloatAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::float, null);
    }

    public function testFloatAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::float, true);
    }

    public function testFloatAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::float, "a string");
    }

    public function testFloatAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_price", Types::float, []);
    }
}
