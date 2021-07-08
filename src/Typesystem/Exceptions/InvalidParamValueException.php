<?php

namespace Invoke\Typesystem\Exceptions;

use Invoke\Typesystem\Typesystem;

class InvalidParamValueException extends TypesystemValidationException
{
    protected string $paramName;

    protected $paramType;
    protected string $paramTypeName;

    protected $value;

    public function __construct(string $paramName, $paramType, $value, ?string $message = null, int $code = 500)
    {
        $this->paramName = $paramName;

        $this->paramType = $paramType;
        $this->paramTypeName = Typesystem::getTypeName($paramType);

        $this->value = $value;

        parent::__construct(
            "INVALID_PARAM_VALUE",
            $message ?? "Invalid \"{$this->paramName}\" value: {$this->value}.",
            $code,
            [
                "param" => $this->paramName,
                "type" => $this->paramTypeName,
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
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
