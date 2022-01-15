<?php

namespace InvokeTests\Typesystem;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Types;
use Invoke\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class IntTest extends TestCase
{
    use SetupInvoke;

    public function testIntShouldNotFail()
    {
        $result = Typesystem::validateParam("some_id", Types::int, 10);
        $this->assertEquals(10, $result);

        $result = Typesystem::validateParam("some_id", Types::int, 1.5);
        $this->assertEquals(1.0, $result);
    }

    public function testIntAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::int, null);
    }

    public function testIntAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::int, true);
    }

    public function testIntAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::int, "a string");
    }

    public function testIntAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_id", Types::int, []);
    }
}
