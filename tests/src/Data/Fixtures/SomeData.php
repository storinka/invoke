<?php

namespace InvokeTests\Data\Fixtures;

use Invoke\Data;
use InvokeTests\Data\Fixtures\Pipes\DoubleValuePipe;

class SomeData extends Data
{
    public string $name;

    public ?string $nullableContent;

    public int $intWithDefault = 123;

    #[DoubleValuePipe]
    public int $intWithPipe;
}