<?php

namespace Invoke\Exceptions;

use Invoke\Types\TypeWithParams;
use Invoke\Utils\Utils;

class RequiredParamNotProvidedException extends PipeException
{
    public function __construct(TypeWithParams $expectedType, string $name)
    {
        $pipeName = Utils::getPipeTypeName($expectedType);

        parent::__construct("Required param \"{$pipeName}::{$name}\" was not provided.");
    }
}
