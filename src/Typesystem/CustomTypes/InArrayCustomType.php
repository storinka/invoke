<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\InvokeError;
use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;

class InArrayCustomType extends CustomType
{
    protected $values = [];

    public function __construct(array $values, $type = Type::String)
    {
        $this->type = $type;
        $this->values = $values;
    }

    public function validate(string $paramName, $value)
    {
        if (!in_array($value, $this->values)) {
            throw new InvokeError("INVALID_VALUE");
        }

        return $value;

    }
}
