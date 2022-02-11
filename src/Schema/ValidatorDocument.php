<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Utils\Utils;
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
            $data["itemType"] = Utils::getSchemaTypeName($validator->itemPipe);
        }

        return [
            "name" => invoke_get_class_name($validator::class),

            "description" => "...",
            "data" => $data,
        ];
    }
}
