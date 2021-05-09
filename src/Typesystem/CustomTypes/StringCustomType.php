<?php

namespace Invoke\Typesystem\CustomTypes;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
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
        if (function_exists("mb_strlen")) {
            $length = mb_strlen($value);
        } else {
            $length = strlen($value);
        }

        if (!is_null($this->minLength)) {
            if ($length < $this->minLength) {
                throw new InvalidParamValueException(
                    $paramName,
                    $this,
                    $value,
                    "min length \"{$this->minLength}\", got \"{$length}\""
                );
            }
        }

        if (!is_null($this->maxLength)) {
            if ($length > $this->maxLength) {
                throw new InvalidParamValueException(
                    $paramName,
                    $this,
                    $value,
                    "max length \"{$this->maxLength}\", got \"{$length}\""
                );
            }
        }

        return $value;
    }
}
