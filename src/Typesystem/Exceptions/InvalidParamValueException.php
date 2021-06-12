<?php

namespace Invoke\Typesystem\Exceptions;

use Invoke\Typesystem\Typesystem;

class InvalidParamValueException extends TypesystemValidationException
{
    protected string $paramName;
    protected string $paramType;
    protected $value;

    public function __construct(string $paramName, $paramType, $value, ?string $message = null, string $error = "INVALID_PARAM_VALUE", int $code = 400)
    {
        $this->paramName = $paramName;
        $this->paramType = Typesystem::getTypeName($paramType);
        $this->value = $value;

        $messageSuffix = $message ?? $this->value;

        parent::__construct(
            $error,
            "Invalid \"{$this->paramName}\" value: {$messageSuffix}.",
            $code,
            [
                "param" => $this->paramName,
                "type" => $this->paramType,
                "value" => $value,
            ]
        );
    }

    /**
     * @return string
     */
    public function getParamName(): string
    {
        return $this->paramName;
    }

    /**
     * @return string
     */
    public function getParamType(): string
    {
        return $this->paramType;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
