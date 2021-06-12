<?php

namespace Invoke\Typesystem\Exceptions;

use Invoke\Typesystem\Typesystem;

class InvalidParamTypeException extends TypesystemValidationException
{
    protected string $paramName;
    protected string $paramType;
    protected string $actualType;

    public function __construct(string $paramName, $paramType, $actualType, string $error = "INVALID_PARAM_TYPE", int $code = 400)
    {
        $this->paramName = $paramName;
        $this->paramType = Typesystem::getTypeName($paramType);
        $this->actualType = Typesystem::getTypeName($actualType);

        parent::__construct(
            $error,
            "Invalid \"{$this->paramName}\" type: expected \"{$this->paramType}\", got \"{$this->actualType}\".",
            $code,
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
