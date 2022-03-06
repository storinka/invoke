<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\SomeType;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

class BasicsTest extends TestCase
{
    protected function fromInput(array|object $input): SomeType
    {
        return Piping::run(new SomeType(), $input);
    }

    protected function isInitializedPropertyValue(object $type, string $property): bool
    {
        $reflection = new \ReflectionProperty($type, $property);

        return $reflection->isInitialized($type);
    }

    public function test(): void
    {
        $input = [
            "name" => "Davyd",
            "intWithPipe" => 2,
            "parameterWithSetter" => "davyd",
        ];

        $assertType = function (SomeType $type) {
            assertEquals("Davyd", $type->name);
            assertEquals(4, $type->intWithPipe);
            assertEquals(123, $type->intWithDefault);
            assertEquals(null, $type->nullableContent);
            assertEquals("DAVYD", $type->parameterWithSetter);
            assertFalse(isset($type->sampleClass));
            assertFalse(isset($type->notParameter));
            assertFalse($this->isInitializedPropertyValue($type, "protectedNotParameter"));
            assertFalse($this->isInitializedPropertyValue($type, "privateNotParameter"));
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
            "parameterWithSetter" => "davyd",
        ];
        $type = $this->fromInput($input);

        $type['name'] = "Davyd2";
        assertTrue(isset($type['name']));
        assertEquals($type['name'], "Davyd2");

        $this->expectException(\RuntimeException::class);
        unset($type['name']);
    }
}