<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;
use InvokeTests\TypeWithParams\Fixtures\Validators\SomeValidator;

class TypeWithValidator extends TypeWithParams
{
    #[SomeValidator]
    public string $string;
}