<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\FloatType;

class FloatTypeTest extends TypeStrictTestCase
{
    public function validInput(): iterable
    {
        return [
            ["3.14", 3.14],
            ["3", 3],
            [3, 3],
            [3.14, 3.14],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            [false],
            [null],
            ["lol"],
            ["1lol"],
        ];
    }

    public function validStrictInput(): iterable
    {
        return [
            [3, 3],
            [3.14, 3.14],
        ];
    }

    public function invalidStrictInput(): iterable
    {
        return [
            [false],
            [null],
            ["lol"],
            ["3.14"],
            ["3"],
        ];
    }


    protected function getType(): Type
    {
        return FloatType::getInstance();
    }

    protected function getTypeName(): string
    {
        return "float";
    }
}
