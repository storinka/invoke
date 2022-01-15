<?php

namespace InvokeTests\Typesystem;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Types;
use Invoke\Typesystem;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;

class TypedArrayTest extends TestCase
{
    use SetupInvoke;

    public function testTypedArrayShouldNotFail()
    {
        $result = Typesystem::validateParam("some_items", Types::ArrayOf(Types::int), [1, 2, 3, 4]);
        $this->assertIsArray($result);

        $result = Typesystem::validateParam("some_items", Types::ArrayOf(Types::int), [1, 3.14]);
        $this->assertIsArray($result);
    }

    public function testTypedArrayAndBoolShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::ArrayOf(Types::int), [1, true]);
    }

    public function testTypedArrayAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::ArrayOf(Types::int), [1, "some string"]);
    }

    public function testTypedArrayAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("some_items", Types::ArrayOf(Types::int), [1, null]);
    }
}
