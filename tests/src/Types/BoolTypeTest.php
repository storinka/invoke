<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\BoolType;

class BoolTypeTest extends TypeStrictTestCase
{
    public function validStrictInput(): iterable
    {
        return [
            [false, false],
            [true, true],
        ];
    }

    public function invalidStrictInput(): iterable
    {
        return [
            ["true"],
            ["false"],
            [0],
            [1],
            [null],
        ];
    }

    public function validInput(): iterable
    {
        return [
            ["true", true],
            ["false", false],
            [false, false],
            [true, true],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["falsy"],
            ["truly"],
            [0],
            [1],
            [null],
        ];
    }

    protected function getType(): Type
    {
        return BoolType::getInstance();
    }

    protected function getTypeName(): string
    {
        return "bool";
    }

}
