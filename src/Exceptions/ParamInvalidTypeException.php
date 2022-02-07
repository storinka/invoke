<?php

namespace Invoke\Exceptions;

use Invoke\Type;
use Invoke\Utils\Utils;

class ParamInvalidTypeException extends InvalidTypeException
{
    public string $path;

    public function __construct(string $path,
                                Type   $pipe,
                                mixed  $value)
    {
        parent::__construct($pipe, $value);

        $pipeName = Utils::getPipeTypeName($pipe);
        $valueType = Utils::getValueTypeName($value);

        $this->message = "Invalid \"{$path}\": expected \"{$pipeName}\", got \"{$valueType}\".";
        $this->path = $path;
    }
}