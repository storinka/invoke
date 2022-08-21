<?php

namespace InvokeTests\Types;

use Invoke\Type;
use Invoke\Types\AnyType;
use function PHPUnit\Framework\assertEquals;

class AnyTypeTest extends TypeBasedTestCase
{
    protected function getType(): Type
    {
        return AnyType::getInstance();
    }

    protected function getTypeName(): string
    {
        return "any";
    }

    public function testValid(): void
    {
        $type = AnyType::getInstance();

        foreach (["lol", 123, 3.4, true, null] as $value) {
            assertEquals($value, $type->run($value));
        }
    }
}