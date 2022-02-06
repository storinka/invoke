<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Validation;
use Invoke\Validations\ArrayOf;

class ValidationDocument extends Data
{
    public string $name;

    public string $description;

    public array $data;

    public function render(Validation $validation): array
    {
//        if ($validation instanceof ArrayOf) {
//            invoke_dd($validation);
//        }

        return [
            "name" => invoke_get_class_name($validation::class),
            "typeName" => $validation->getTypeName(),
            "description" => "...",
            "data" => $validation instanceof ArrayOf ? [
                "itemType" => TypeDocument::from($validation->itemPipe),
            ] : $validation->getValidationData(),
        ];
    }
}