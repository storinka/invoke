<?php

namespace InvokeTests\Types;

use Invoke\Types\AnyType;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;

class AnyTypeTest extends TestCase
{
    public function testValid(): void
    {
        $type = AnyType::getInstance();

        foreach (["lol", 123, 3.4, true, null] as $value) {
            assertEquals($value, $type->pass($value));
        }
    }
}