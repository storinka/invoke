<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\TypeWithTypeProperty;
use function PHPUnit\Framework\assertEquals;

class TypeParameterTest extends TestCase
{
    protected function fromInput(array $input): TypeWithTypeProperty
    {
        return Piping::run(new TypeWithTypeProperty(), $input);
    }

    public function test()
    {
        $type = $this->fromInput([
            "someType" => [
                "numeric" => 123
            ]
        ]);

        assertEquals(123, $type->someType->numeric);

        $type = $this->fromInput([
            "someType" => [
                "@type" => "AnotherSomeType",
                "numeric" => 123
            ]
        ]);

        assertEquals(123, $type->someType->numeric);

        $type = $this->fromInput([
            "someType" => "123"
        ]);

        assertEquals("123", $type->someType);
    }

    public function testToArray()
    {
        $input = [
            "someType" => [
                "numeric" => 123
            ]
        ];

        $type = $this->fromInput($input);

        assertEquals($input, $type->toArray());
    }
}