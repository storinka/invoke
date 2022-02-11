<?php

namespace InvokeTests\Types;

use Invoke\Types\IntType;

class IntTypeStrictTest extends TypeTestCase
{
    protected static bool $strict = true;

    public function validInput(): iterable
    {
        return [
            [14, 14],
            [0, 0],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["13"],
            ["lol"],
            ["true"],
            [true],
            [null],
        ];
    }


    protected function getType(): IntType
    {
        return IntType::getInstance();
    }
}
