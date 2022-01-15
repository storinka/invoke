<?php

namespace InvokeTests\Typesystem;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem;
use Invoke\Types;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class ArrayTest extends TestCase
{
    use SetupInvoke;

    public function testArrayShouldNotFail()
    {
        $result = Typesystem::validateParam("some_items", Types::array, [1, 2, "kek", true]);
        $this->assertIsArray($result);
    }

    public function testArrayAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::array, null);
    }

    public function testArrayAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::array, 10);
    }

    public function testArrayAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::array, true);
    }

    public function testArrayAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::array, "some name");
    }
}
