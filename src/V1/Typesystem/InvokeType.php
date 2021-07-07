<?php

namespace Invoke\V1\Typesystem;

use ArrayAccess;
use JsonSerializable;

interface InvokeType extends JsonSerializable, ArrayAccess
{
    public static function params(): array;

    public function getValidatedParams(): array;
}
