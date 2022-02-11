<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\NullType;

class NullTypeTest extends TypeTestCase
{
    public function validInput(): iterable
    {
        return [
            [null, null],
            ["NULL", null],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["NuLL"],
            [false],
            [0],
        ];
    }

    protected function getType(): Type
    {
        return NullType::getInstance();
    }
}
