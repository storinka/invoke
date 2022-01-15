<?php

namespace Invoke\Exceptions;

use Invoke\Typesystem;

class InvalidParamTypeException extends TypesystemValidationException
{
    protected string $paramName;

    protected $paramType;
    protected string $paramTypeName;

    protected $actualType;
    protected string $actualTypeName;

    public function __construct(string  $paramName,
                                        $paramType,
                                        $actualType,
                                ?string $message = null,
                                int     $code = 500)
    {
        $this->paramName = $paramName;

        $this->paramType = $paramType;
        $this->paramTypeName = Typesystem::getTypeName($paramType);

        $this->actualType = $actualType;
        $this->actualTypeName = Typesystem::getTypeName($actualType);

        parent::__construct(
            "INVALID_PARAM_TYPE",
            $message ?? "Invalid \"{$this->paramName}\" type: expected \"{$this->paramTypeName}\", got \"{$this->actualTypeName}\".",
            $code,
            [
                "param" => $paramName,
                "type" => $this->paramTypeName,
                "actual_type" => $this->actualTypeName,
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
     * @return mixed
     */
    public function getParamType()
    {
        return $this->paramType;
    }

    /**
     * @return string
     */
    public function getParamTypeName(): string
    {
        return $this->paramTypeName;
    }

    /**
     * @return mixed
     */
    public function getActualType()
    {
        return $this->actualType;
    }

    /**
     * @return string
     */
    public function getActualTypeName(): string
    {
        return $this->actualTypeName;
    }
}
