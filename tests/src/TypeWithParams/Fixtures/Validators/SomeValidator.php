<?php

namespace InvokeTests\TypeWithParams\Fixtures\Validators;

use Invoke\Validator;
use InvokeTests\TypeWithParams\Fixtures\Validators\Exceptions\SomeValidatorException;

#[\Attribute]
class SomeValidator implements Validator
{
    public function pass(mixed $value): mixed
    {
        if ($value === "fail") {
            throw new SomeValidatorException();
        }
        return $value;
    }
}