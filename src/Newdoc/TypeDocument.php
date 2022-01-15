<?php

namespace Invoke\Newdoc;

use Invoke\AsData;
use Invoke\Data;
use Invoke\Type;
use Invoke\Typesystem;
use Invoke\Validation;
use Invoke\Validations\TypeWithValidations;

class TypeDocument extends Data
{
    public string $name;

    public ?string $class;

    public bool $isBuiltin;

    public bool $isData;

    public bool $isCustom;

    public array $validations;

    public array $tags;

    public function render($type): array
    {
        $name = Typesystem::getTypeName($type);

        return [
            "name" => $name,
            "class" => is_string($type) && class_exists($type) ? $type : null,

            "isBuiltin" => Typesystem::isBuiltinType($type),
            "isData" => is_string($type) && is_subclass_of($type, AsData::class),
            "isCustom" => $type instanceof Type,

            "validations" => $type instanceof TypeWithValidations ? array_map(function (Validation $validation) {
                return [
                    "name" => invoke_get_class_name($validation::class),
                    "string" => (string)$validation,
                ];
            }, $type->getValidations()) : [],

            "tags" => [],
        ];
    }
}