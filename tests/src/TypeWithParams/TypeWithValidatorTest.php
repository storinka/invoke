<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\TypeWithValidator;
use function PHPUnit\Framework\assertEquals;

class TypeWithValidatorTest extends TestCase
{
    public function testInvalidValidator()
    {
        /** @var TypeWithValidator $type */
        $type = Piping::run(new TypeWithValidator(), ["string" => "123456"]);

        assertEquals("123456", $type->string);
    }
}