<?php

declare(strict_types=1);

namespace InvokeTests\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Type;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;

abstract class TypeTestCase extends TestCase
{
    protected static bool $strict = false;

    abstract protected function getType(): Type;

    protected function setUp(): void
    {
        static::$invokeConfig["inputMode"]["convertStrings"] = !static::$strict;

        parent::setUp();
    }

    abstract public function validInput(): iterable;

    abstract public function invalidInput(): iterable;

    /** @dataProvider validInput */
    public function testValid(mixed $value, mixed $expected): void
    {
        assertEquals($expected, $this->getType()->pass($value));
    }

    /** @dataProvider invalidInput */
    public function testInvalid(mixed $invalid): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->getType()->pass($invalid);
    }
}
