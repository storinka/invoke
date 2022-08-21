<?php

declare(strict_types=1);

namespace InvokeTests\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Type;
use function PHPUnit\Framework\assertEquals;

abstract class TypeTestCase extends TypeBasedTestCase
{
    abstract protected function getType(): Type;

    abstract public function validInput(): iterable;

    abstract public function invalidInput(): iterable;

    /** @dataProvider validInput */
    public function testValid(mixed $value, mixed $expected): void
    {
        assertEquals($expected, $this->getType()->run($value));
    }

    /** @dataProvider invalidInput */
    public function testInvalid(mixed $invalid): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->getType()->run($invalid);
    }
}
