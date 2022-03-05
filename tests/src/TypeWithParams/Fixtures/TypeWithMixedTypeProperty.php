<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;

class TypeWithMixedTypeProperty extends TypeWithParams
{
    public AnotherSomeType|AnotherAnotherSomeType $mixedSomeType;
}