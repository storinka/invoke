<?php

namespace Invoke\Newdoc;

use Invoke\AsData;
use Invoke\Data;
use Invoke\Type;
use Invoke\Types;
use Invoke\Typesystem;
use Invoke\Validation;
use Invoke\Validation\ArrayOf;
use Invoke\Validation\TypeWithValidations;

class TypeDocument extends Data
{
    public string $name;

    public ?string $class;

    public bool $isBuiltin;

    public bool $isData;

    public bool $isCustom;

    public bool $isUnion;

    #[ArrayOf(TypeDocument::class)]
    public array $subtypes;

    #[ArrayOf(ParamDocument::class)]
    public array $params;

    #[ArrayOf(ValidationDocument::class)]
    public array $validations;

    #[ArrayOf(Types::string)]
    public array $tags;

    public function render($type): array
    {
        $name = Typesystem::getTypeName($type);
        $isClass = is_string($type) && class_exists($type);
        $isBuiltin = Typesystem::isBuiltinType($type);
        $isData = $isClass && is_subclass_of($type, AsData::class);
        $isCustom = $type instanceof Type;
        $isUnion = is_array($type);

        $paramsDocuments = [];

        if ($isData) {
            $typeInstance = (new $type);

            foreach ($typeInstance->getDataParams() as $paramName => $paramType) {
                $paramsDocuments[] = [
                    "name" => $paramName,
                    "type" => $paramType
                ];
            }
        }

        return [
            "name" => $name,

            "isBuiltin" => $isBuiltin,
            "isData" => $isData,
            "isCustom" => $isCustom,
            "isUnion" => $isUnion,

            "subtypes" => $isUnion ? TypeDocument::many($type) : [],

            "validations" => $type instanceof TypeWithValidations ? array_map(function (Validation $validation) {
                return ValidationDocument::from([
                    "name" => $validation::getName(),
                    "description" => $validation->getDescription(),

                    "class" => $validation::class,
                ]);
            }, $type->getValidations()) : [],

            "tags" => [],

            "class" => $isClass ? $type : null,

            "params" => ParamDocument::many($paramsDocuments),
        ];
    }
}