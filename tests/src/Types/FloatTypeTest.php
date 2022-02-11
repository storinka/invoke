<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\FloatType;

class FloatTypeTest extends TypeTestCase
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

    protected function getType(): Type
    {
        return FloatType::getInstance();
    }
}
