<?php

namespace InvokeTests\Types;

use Invoke\Container;
use Invoke\Invoke;

abstract class TypeStrictTestCase extends TypeTestCase
{
    abstract public function validStrictInput(): iterable;

    abstract public function invalidStrictInput(): iterable;

    protected function enableStrict(bool $enable = true): void
    {
        Container::get(Invoke::class)->setConfig([
            "inputMode" => [
                "convertStrings" => !$enable
            ]
        ]);
    }

    /** @dataProvider validStrictInput */
    public function testStrictValid(mixed $value, mixed $expected): void
    {
        $this->enableStrict();

        parent::testValid($value, $expected);

        $this->enableStrict(false);
    }

    /** @dataProvider invalidStrictInput */
    public function testStrictInvalid(mixed $value): void
    {
        $this->enableStrict();

        parent::testInvalid($value);

        $this->enableStrict(false);
    }
}