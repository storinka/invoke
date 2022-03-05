<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\SomeType;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class BasicsTest extends TestCase
{
    protected function fromInput(array|object $input): SomeType
    {
        return Piping::run(new SomeType(), $input);
    }

    public function test(): void
    {
        $input = [
            "name" => "Davyd",
            "intWithPipe" => 2,
        ];

        $assertType = function (SomeType $type){
            assertEquals("Davyd", $type->name);
            assertEquals(4, $type->intWithPipe);
            assertEquals(123, $type->intWithDefault);
            assertEquals(null, $type->nullableContent);
        };

        $type = $this->fromInput($input);
        $assertType($type);

        $type = $this->fromInput((object)$input);
        $assertType($type);
    }

    public function testAccessArray(): void
    {
        $input = [
            "name" => "Davyd",
            "intWithPipe" => 2,
        ];
        $type = $this->fromInput($input);

        $type['name'] = "Davyd2";
        assertTrue(isset($type['name']));
        assertEquals($type['name'], "Davyd2");

        $this->expectException(\RuntimeException::class);
        unset($type['name']);
    }
}