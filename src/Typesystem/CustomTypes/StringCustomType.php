<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\InvokeError;
use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;

class StringCustomType extends CustomType
{
    protected $type = Type::String;

    protected $minLength;
    protected $maxLength;

    public function __construct($minLength = null, $maxLength = null)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    public function validate(string $paramName, $value)
    {
        $length = strlen($value);

        if (!is_null($this->minLength)) {
            if ($length < $this->minLength) {
                throw new InvokeError("INVALID_PARAM_STRING_MIN_LENGTH_VALUE");
            }
        }

        if (!is_null($this->maxLength)) {
            if ($length > $this->maxLength) {
                throw new InvokeError("INVALID_PARAM_STRING_MAX_LENGTH_VALUE");
            }
        }

        return $value;
    }
}
