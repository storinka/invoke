<?php

declare(strict_types=1);

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\EnumType;
use InvokeTests\Types\Fixtures\IntBackedTestEnum;

class EnumIntTypeTest extends EnumTypeBasedTestCase
{
    public function validInput(): iterable
    {
        return [
            ["1", IntBackedTestEnum::One],
            [1, IntBackedTestEnum::One],
            [2, IntBackedTestEnum::Two],
            [IntBackedTestEnum::One, IntBackedTestEnum::One]
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            ["One"],
            [null],
            [true]
        ];
    }

    /** @dataProvider validInput */
    public function testValid(mixed $value, mixed $expected): void
    {
        parent::testValid($value, $expected);
    }

    protected function getType(): Type
    {
        return new EnumType(IntBackedTestEnum::class);
    }
}
