<?php

namespace Invoke\Schema;

use BackedEnum;
use Invoke\Data;
use Invoke\Pipe;
use Invoke\Toolkit\Validators\ArrayOf;
use Invoke\Type;
use Invoke\Types\WrappedType;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use Invoke\Validator;
use ReflectionClass;
use ReflectionException;

class TypeDocument extends Data
{
    public string $name;

    public string $schemaTypeName;

    public ?string $summary;

    public ?string $description;

    public bool $isBuiltin;

    public bool $isData;

    public bool $isUnion;

    public bool $isFile;

    public bool $isEnum;

    #[ArrayOf("string")]
    public array $unionTypes;

    #[ArrayOf(["string", "int"])]
    public array $enumValues;

    #[ArrayOf(ParamDocument::class)]
    public array $params;

    #[ArrayOf(ValidatorDocument::class)]
    public array $validators;

    /**
     * @throws ReflectionException
     */
    public function render(Type $type): array
    {
        $name = Utils::getPipeTypeName($type);
        $schemaTypeName = Utils::getSchemaTypeName($type);

        if ($type instanceof WrappedType) {
            $type = $type->typeClass;
        }

        $validators = [];
        if ($type instanceof Validator) {
            $validators = [$type];
        }

        $isBuiltin = Utils::isPipeTypeBuiltin($type);
        $isData = Utils::isPipeTypeData($type);
        $isUnion = Utils::isPipeTypeUnion($type);
        $isFile = Utils::isPipeTypeBinary($type);
        $isEnum = Utils::isPipeTypeEnum($type);

        $reflectionClass = new ReflectionClass($type);

        $comment = ReflectionUtils::extractComment($reflectionClass);
        $summary = $comment["summary"];
        $description = $comment["description"];

        $params = [];
        if ($isData) {
            $params = ReflectionUtils::extractParamsPipes($reflectionClass);
        }

        $unionTypes = $isUnion ? $type->pipes : [];

        return [
            "name" => $name,
            "schemaTypeName" => $schemaTypeName,
            "summary" => $summary,
            "description" => $description,

            "isBuiltin" => $isBuiltin,
            "isData" => $isData,
            "isUnion" => $isUnion,
            "isFile" => $isFile,
            "isEnum" => $isEnum,

            "unionTypes" => array_map(fn(Pipe $pipe) => Utils::getSchemaTypeName($pipe), $unionTypes),
            "enumValues" => $isEnum ? array_map(fn(BackedEnum $pipe) => $pipe->value, $type->enumClass::cases()) : [],
            "params" => ParamDocument::many($params),
            "validators" => ValidatorDocument::many($validators),
        ];
    }
}
