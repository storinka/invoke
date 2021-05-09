<?php

namespace Invoke\Typesystem\Exceptions;

use Invoke\Typesystem\Typesystem;

class InvalidParamValueException extends TypesystemValidationException
{
    protected string $paramName;
    protected string $paramType;
    protected string $value;

    public function __construct(string $paramName, $paramType, $value, ?string $message = null)
    {
        $this->paramName = $paramName;
        $this->paramType = Typesystem::getTypeName($paramType);
        $this->value = $value;

        $messageSuffix = $message ?? $this->value;

        parent::__construct(
            "INVALID_PARAM_VALUE",
            "Invalid \"{$this->paramName}\" value: {$messageSuffix}.",
            400,
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
