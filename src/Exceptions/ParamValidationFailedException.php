<?php

namespace Invoke\Exceptions;

use Invoke\Pipe;

class ParamValidationFailedException extends ValidationFailedException
{
    public string $path;

    public function __construct(string $path,
                                Pipe   $pipe,
                                mixed  $value)
    {
        parent::__construct($pipe, $value);

        $pipeName = $pipe->getTypeName();
        $valueType = $pipe->getValueTypeName($value);

        $this->message = "Invalid \"{$path}\": expected \"{$pipeName}\", got \"{$valueType}\".";
        $this->path = $path;
    }
}