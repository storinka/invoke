<?php

namespace Invoke\Typesystem;

use Invoke\InvokeError;

class TypesystemValidationException extends InvokeError
{
    protected string $paramName;
    protected string $paramType;
    protected string $actualType;

    public function __construct($paramName, $paramType, $actualType)
    {
        $this->paramName = $paramName;
        $this->paramType = Typesystem::getTypeName($paramType);
        $this->actualType = Typesystem::getTypeName($actualType);

        parent::__construct("INVALID_PARAM_TYPE", 400, [
            "param" => $paramName,
            "type" => $this->paramType,
            "actual_type" => $this->actualType,
        ]);

        $this->message = "Invalid \"{$this->paramName}\" type: expected \"{$this->paramType}\", got \"{$this->actualType}\".";
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
