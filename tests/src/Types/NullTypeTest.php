<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\NullType;

class NullTypeTest extends TypeStrictTestCase
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

    public function validStrictInput(): iterable
    {
        return [
            [null, null],
        ];
    }

    public function invalidStrictInput(): iterable
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

    protected function getTypeName(): string
    {
        return "null";
    }
}
