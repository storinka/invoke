<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\NullType;

class NullTypeStrictTest extends TypeTestCase
{
    protected static bool $strict = true;

    public function validInput(): iterable
    {
        return [
            [null, null],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["NuLL"],
            ["NULL"],
            [false],
            [0],
        ];
    }

    protected function getType(): Type
    {
        return NullType::getInstance();
    }
}
