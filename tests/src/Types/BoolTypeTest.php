<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\BoolType;

class BoolTypeTest extends TypeTestCase
{
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
}
