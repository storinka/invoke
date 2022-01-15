<?php

namespace InvokeTests\Typesystem;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Types;
use Invoke\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class BoolTest extends TestCase
{
    use SetupInvoke;

    public function testBoolShouldNotFail()
    {
        $result = Typesystem::validateParam("is_something_true", Types::bool, true);
        $this->assertEquals(true, $result);

        $result = Typesystem::validateParam("is_something_true", Types::bool, false);
        $this->assertEquals(false, $result);
    }

    public function testBoolAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::bool, null);
    }

    public function testBoolAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::bool, 10);
    }

    public function testBoolAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::bool, "a string");
    }

    public function testBoolAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::bool, []);
    }
}
