<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\BoolType;

class BoolTypeStrictTest extends TypeTestCase
{
    protected static bool $strict = true;

    public function validInput(): iterable
    {
        return [
            [false, false],
            [true, true],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["true"],
            ["false"],
            [0],
            [1],
            [null],
        ];
    }

    protected function getType(): Type
    {
        return BoolType::getInstance();
    }
}
