<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\ArrayType;

class ArrayTypeTest extends TypeTestCase
{
    public function validInput(): iterable
    {
        return [
            [[1, 2], [1, 2]]
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["[1,2]"],
            [null],
            [false],
            [3]
        ];
    }

    protected function getType(): Type
    {
        return ArrayType::getInstance();
    }

    protected function getTypeName(): string
    {
        return "array";
    }
}
