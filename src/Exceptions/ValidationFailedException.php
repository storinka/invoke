<?php

namespace Invoke\Exceptions;

use Invoke\Pipe;

class ValidationFailedException extends PipeException
{
    public readonly Pipe $pipe;
    public readonly mixed $value;

    public function __construct(Pipe $pipe, mixed $value)
    {
        $pipeName = $pipe->getTypeName();
        $valueType = $pipe->getValueTypeName($value);

        $this->pipe = $pipe;
        $this->value = $value;

        parent::__construct("Expected \"{$pipeName}\", got \"{$valueType}\".");
    }
}