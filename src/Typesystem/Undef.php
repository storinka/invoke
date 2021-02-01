<?php

namespace Invoke\Typesystem;

use JsonSerializable;

class Undef implements JsonSerializable
{
    public function jsonSerialize()
    {
        return null;
    }
}
