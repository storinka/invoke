<?php

namespace Invoke\Types;

use Invoke\Data;
use Invoke\Support\ReadonlyParams;

class SuccessResponseData extends Data
{
    use ReadonlyParams;

    public readonly mixed $result;
}