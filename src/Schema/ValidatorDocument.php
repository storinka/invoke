<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Validator;
use Invoke\Validators\ArrayOf;

class ValidatorDocument extends Data
{
    public string $name;

    public string $description;

    public array $data;

    public function render(Validator $validator): array
    {
        $data = [];

        if ($validator instanceof ArrayOf) {
            $data["itemType"] = $validator->itemPipe->getTypeName();
        }

        return [
            "name" => $validator->getValidatorName(),
            "typeName" => $validator->getTypeName(),

            "description" => "...",
            "data" => $data,
        ];
    }
}