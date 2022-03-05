<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;

class TypeWithInjectProperty extends TypeWithParams
{
    public SomeType $someType;

}