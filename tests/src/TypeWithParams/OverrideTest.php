<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\TypeWithOverride;
use function PHPUnit\Framework\assertEquals;

class OverrideTest extends TestCase
{
    public function test()
    {
        /** @var TypeWithOverride $type */
        $type = Piping::run(new TypeWithOverride(), [
            "val" => "name"
        ]);

        assertEquals("NAME", $type->name);
    }
}