<?php

namespace Invoke\Exceptions;

use Invoke\Pipe;

class RequiredParamNotProvidedException extends PipeException
{
    public function __construct(Pipe $pipe, string $name)
    {
        $pipeName = $pipe->getTypeName();

        parent::__construct("Required param \"{$pipeName}::{$name}\" was not provided.");
    }
}