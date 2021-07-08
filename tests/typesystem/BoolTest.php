<?php

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use PHPUnit\Framework\TestCase;

class BoolTest extends TestCase
{
    public function testBoolShouldNotFail()
    {
        $result = Typesystem::validateParam("is_something_true", Types::Bool, true);
        $this->assertEquals(true, $result);

        $result = Typesystem::validateParam("is_something_true", Types::Bool, false);
        $this->assertEquals(false, $result);
    }

    public function testBoolAndNullShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::Bool, null);
    }

    public function testBoolAndIntShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::Bool, 10);
    }

    public function testBoolAndStringShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::Bool, "a string");
    }

    public function testBoolAndArrayShouldFail()
    {
        $this->expectException(InvalidParamTypeException::class);
        Typesystem::validateParam("is_something_true", Types::Bool, []);
    }
}
