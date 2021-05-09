<?php

namespace Invoke\Typesystem\Exceptions;

use Invoke\Typesystem\Typesystem;

class InvalidParamTypeException extends TypesystemValidationException
{
    protected string $paramName;
    protected string $paramType;
    protected string $actualType;

    public function __construct($paramName, $paramType, $actualType)
    {
        $this->paramName = $paramName;
        $this->paramType = Typesystem::getTypeName($paramType);
        $this->actualType = Typesystem::getTypeName($actualType);

        parent::__construct(
            "INVALID_PARAM_TYPE",
            "Invalid \"{$this->paramName}\" type: expected \"{$this->paramType}\", got \"{$this->actualType}\".",
            400,
            [
                "param" => $paramName,
                "type" => $this->paramType,
                "actual_type" => $this->actualType,
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
    public function getActualType(): string
    {
        return $this->actualType;
    }
}
