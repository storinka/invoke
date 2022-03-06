<?php

namespace InvokeTests\Types;

use Invoke\Support\HasDynamicTypeName;
use Invoke\Types\IntType;
use Invoke\Types\StringType;
use Invoke\Types\UnionType;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

class UnionTypeTest extends TestCase
{
    public function testBasics()
    {
        $type = new UnionType([StringType::getInstance(), IntType::getInstance()]);

        assertEquals("union", $type::invoke_getTypeName());

        assertInstanceOf(HasDynamicTypeName::class, $type);
        assertEquals(StringType::invoke_getTypeName() . " | " . IntType::invoke_getTypeName(), $type->invoke_getDynamicTypeName());
    }
}