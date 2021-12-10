<?php

namespace InvokeTests\Typesystem;

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    use SetupInvoke;

    public function testArrayShouldNotFail()
    {
        $result = Typesystem::validateParam("some_items", Types::Array, [1, 2, "kek", true]);
        $this->assertIsArray($result);
    }

    public function testArrayAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::Array, null);
    }

    public function testArrayAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::Array, 10);
    }

    public function testArrayAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::Array, true);
    }

    public function testArrayAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::Array, "some name");
    }
}
