<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\StringType;

class StringTypeTest extends TypeTestCase
{
    public function validInput(): iterable
    {
        return [
            ["vasya", "vasya"]
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            [null],
            [true],
            [false],
            [3],
        ];
    }

    protected function getType(): Type
    {
        return StringType::getInstance();
    }

    protected function getTypeName(): string
    {
        return "string";
    }
}
