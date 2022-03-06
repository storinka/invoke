<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Type;

class SomeType implements Type
{
    public string $publicProperty = "123";

    protected string $protectedProperty = "123";

    private string $privateProperty = "123";

    public function pass(mixed $value): mixed
    {
        return $value;
    }

    public static function invoke_getTypeName(): string
    {
        return "some";
    }
}