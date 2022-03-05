<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\AnotherAnotherSomeType;
use InvokeTests\TypeWithParams\Fixtures\AnotherSomeType;
use InvokeTests\TypeWithParams\Fixtures\TypeWithMixedTypeProperty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

class MixedTypePropertyTest extends TestCase
{
    protected function fromInput(array $input): TypeWithMixedTypeProperty
    {
        return Piping::run(new TypeWithMixedTypeProperty(), $input);
    }

    public function test()
    {
        $type = $this->fromInput([
            "mixedSomeType" => [
                "@type" => "AnotherSomeType",
                "numeric" => 123
            ]
        ]);

        assertInstanceOf(AnotherSomeType::class, $type->mixedSomeType);
        assertEquals(123, $type->mixedSomeType->numeric);

        $type = $this->fromInput([
            "mixedSomeType" => [
                "@type" => "AnotherAnotherSomeType",
                "string" => "123"
            ]
        ]);

        assertInstanceOf(AnotherAnotherSomeType::class, $type->mixedSomeType);
        assertEquals("123", $type->mixedSomeType->string);
    }
}