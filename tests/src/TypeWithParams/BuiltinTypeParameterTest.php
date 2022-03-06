<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Piping;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\SomeType;
use InvokeTests\TypeWithParams\Fixtures\SomeTypeWithParamsWithTypeParam;
use function PHPUnit\Framework\assertEquals;

class BuiltinTypeParameterTest extends TestCase
{
    public function test()
    {
        $input = [
            "someClass" => new SomeType()
        ];
        /** @var SomeTypeWithParamsWithTypeParam $type */
        $type = Piping::run(new SomeTypeWithParamsWithTypeParam(), $input);

        $excepted = [
            "someClass" => ["publicProperty" => "123"]
        ];

        assertEquals($excepted, $type->toArray());
    }
}