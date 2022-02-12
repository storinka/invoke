<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Toolkit\Validators\ArrayOf;
use Invoke\Utils\Utils;
use Invoke\Validator;

use function Invoke\Utils\get_class_name;

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
            "name" => get_class_name($validator::class),

            "description" => "...",
            "data" => $data,
        ];
    }
}
