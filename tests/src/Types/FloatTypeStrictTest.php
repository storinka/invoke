<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\FloatType;

class FloatTypeStrictTest extends TypeTestCase
{
    protected static bool $strict = true;

    public function validInput(): iterable
    {
        return [
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
            ["3.14"],
            ["3"],
        ];
    }

    protected function getType(): Type
    {
        return FloatType::getInstance();
    }
}
