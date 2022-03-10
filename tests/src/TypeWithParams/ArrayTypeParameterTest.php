<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\TypeWithArrayTypeProperty;
use function PHPUnit\Framework\assertEquals;

class ArrayTypeParameterTest extends TestCase
{
    protected function fromInput(array $input): TypeWithArrayTypeProperty
    {
        return Piping::run(new TypeWithArrayTypeProperty(), $input);
    }

    public function test(): void
    {
        $type = $this->fromInput([
            "arrayPropertyType" => [
                $this->fromInput(["arrayPropertyType" => [1, 2, 3]]),
                $this->fromInput(["arrayPropertyType" => [(object)[1, 2, 3]]]),
                (object)[$this->fromInput(["arrayPropertyType" => [(object)[1, 2, 3]]])]
            ]
        ]);

        assertEquals([
            "arrayPropertyType" => [
                ["arrayPropertyType" => [1, 2, 3]],
                ["arrayPropertyType" => [[1, 2, 3]]],
                [["arrayPropertyType" => [[1, 2, 3]]]],
            ]
        ], $type->toArray());
    }
}