<?php

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use PHPUnit\Framework\TestCase;

class TypedArrayTest extends TestCase
{
    public function testTypedArrayShouldNotFail()
    {
        $result = Typesystem::validateParam("some_items", Types::ArrayOf(Types::Int), [1, 2, 3, 4]);
        $this->assertIsArray($result);

        $result = Typesystem::validateParam("some_items", Types::ArrayOf(Types::Int), [1, 3.14]);
        $this->assertIsArray($result);
    }

    public function testTypedArrayAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::ArrayOf(Types::Int), [1, true]);
    }

    public function testTypedArrayAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::ArrayOf(Types::Int), [1, "some string"]);
    }

    public function testTypedArrayAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::ArrayOf(Types::Int), [1, null]);
    }
}
