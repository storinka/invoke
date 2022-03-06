<?php

namespace InvokeTests\TypeWithParams\Fixtures;

use Invoke\Support\TypeWithParams;
use function mb_strtoupper;

class TypeWithOverride extends TypeWithParams
{
    public string $name;

    public function override(array $input): array
    {
        return [
            "name" => mb_strtoupper($input["val"])
        ];
    }
}