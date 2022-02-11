<?php

namespace InvokeTests\Types;

use Invoke\Types\IntType;

class IntTypeTest extends TypeTestCase
{
    public function validInput(): iterable
    {
        return [
            ["14", 14],
            [0, 0],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["lol"],
            [true],
            [null],
            ["true"],
            ["false"],
            [3.14]
        ];
    }


    protected function getType(): IntType
    {
        $this->getContainer()->singleton(IntType::class, IntType::class);

        return $this->getContainer()->get(IntType::class);
    }
}
