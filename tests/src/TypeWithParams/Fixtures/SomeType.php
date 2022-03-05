<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;
use InvokeTests\TypeWithParams\Fixtures\Pipes\DoubleValuePipe;

class SomeType extends TypeWithParams
{
    public string $name;

    public ?string $nullableContent;

    public int $intWithDefault = 123;

    #[DoubleValuePipe]
    public int $intWithPipe;
}