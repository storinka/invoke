<?php

namespace Invoke\Newdoc;

use Invoke\AsData;
use Invoke\Data;
use Invoke\Type;
use Invoke\Types;
use Invoke\Types\HttpFile;
use Invoke\Typesystem;
use Invoke\Utils\ReflectionUtils;
use Invoke\Validation;
use Invoke\Validation\ArrayOf;
use Invoke\Validation\MultipleValidations;

class TypeDocument extends Data
{
    public string $name;

    public ?string $class;

    public ?string $summary;

    public ?string $description;

    public bool $isBuiltin;

    public bool $isData;

    public bool $isCustom;

    public bool $isUnion;

    public bool $isFile;

    public bool $isOptional;

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
        $isFile = $isClass && (is_subclass_of($type, HttpFile::class) || $type === HttpFile::class);
        $isAny = $type === Types::T;
        $isOptional = $isUnion && in_array(Types::null, $type);

        $paramsDocuments = [];

        $validations = [];

        $summary = null;
        $description = null;

        if ($isClass) {
            $reflectionClass = new \ReflectionClass($type);
            $comment = ReflectionUtils::parseComment($reflectionClass);

            $summary = $comment["summary"];
            $description = $comment["description"];
        } else if ($isBuiltin) {
            $summary = [
                Types::int => "Integer type.",
                Types::string => "String type.",
                Types::float => "Float type.",
                Types::array => "Array type.",
                Types::bool => "Boolean type.",
                Types::null => "Null type.",
                Types::T => "Any type.",
            ][$type];

            $description = [
                Types::int => "Example: <code>123</code>",
                Types::string => "Example: <code>\"Diana\"</code>",
                Types::float => "Example: <code>3.14</code>",
                Types::array => "Example: <code>[1, 2, 3]</code>",
                Types::bool => "Example: <code>true</code>",
                Types::null => "Example: <code>null</code>",
            ][$type];
        } else if ($isAny) {
            $summary = "Any type.";
        }

        if ($isData) {
            $typeInstance = (new $type);

            foreach ($typeInstance->getDataParams() as $paramName => $paramType) {
                $paramsDocuments[] = [
                    "name" => $paramName,
                    "type" => $paramType
                ];
            }
        }

        if ($type instanceof MultipleValidations) {
            $validations = array_map(function (Validation $validation) {
                return [
                    "name" => $validation::getName(),
                    "description" => $validation->getDescription(),

                    "class" => $validation::class,
                ];
            }, $type->getValidations());
        }

        return [
            "name" => $name,
            "summary" => $summary,
            "description" => $description,

            "isBuiltin" => $isBuiltin,
            "isData" => $isData,
            "isCustom" => $isCustom,
            "isUnion" => $isUnion,
            "isFile" => $isFile,
            "isAny" => $isAny,
            "isOptional" => $isOptional,

            "subtypes" => $isUnion ? TypeDocument::many($type) : [],

            "validations" => ValidationDocument::many($validations),

            "tags" => [],

            "class" => $isClass ? $type : null,

            "params" => ParamDocument::many($paramsDocuments),
        ];
    }
}