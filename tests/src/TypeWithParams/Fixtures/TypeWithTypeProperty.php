<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;

class TypeWithTypeProperty extends TypeWithParams
{
    public AnotherSomeType|string $someType;

}