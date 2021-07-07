<?php

namespace Invoke\V1\Typesystem\Exceptions;

use Invoke\V1\Typesystem\TypesystemV1;

class InvalidParamTypeExceptionV1 extends TypesystemValidationExceptionV1
{
    protected string $paramName;
    protected string $paramType;
    protected string $actualType;

    public function __construct(string $paramName, $paramType, $actualType)
    {
        $this->paramName = $paramName;
        $this->paramType = TypesystemV1::getTypeName($paramType);
        $this->actualType = TypesystemV1::getTypeName($actualType);

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
