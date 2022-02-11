<?php

declare(strict_types=1);

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\EnumType;
use InvokeTests\Types\Fixtures\StringBackedTestEnum;

class StringEnumTypeTest extends TypeTestCase
{
    public function validInput(): iterable
    {
        return [
            ["1", StringBackedTestEnum::One],
            ["2", StringBackedTestEnum::Two],
        ];
    }

    public function invalidInput(): iterable
    {
        return [
            [1],
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
        return new EnumType(StringBackedTestEnum::class);
    }
}
