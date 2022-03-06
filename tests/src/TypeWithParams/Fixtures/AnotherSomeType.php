<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;

class AnotherSomeType extends TypeWithParams
{
    public int $numeric;

    protected AnotherAnotherSomeType $anotherAnotherSomeType;
}