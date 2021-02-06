<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\InvokeError;
use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;

class IntCustomType extends CustomType
{
    protected $type = Type::Int;

    protected $minValue;
    protected $maxMax;

    public function __construct($minValue = null, $maxValue = null)
    {
        $this->minValue = $minValue;
        $this->maxMax = $maxValue;
    }

    public function validate(string $paramName, $value)
    {
        if (!is_null($this->minValue)) {
            if ($value < $this->minValue) {
                throw new InvokeError("INVALID_PARAM_INT_MIN_VALUE");
            }
        }

        if (!is_null($this->maxMax)) {
            if ($value > $this->maxMax) {
                throw new InvokeError("INVALID_PARAM_INT_MAX_VALUE");
            }
        }

        return $value;
    }
}
