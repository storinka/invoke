<?php

namespace Invoke\Schema;

use Invoke\Data;
use Invoke\Pipe;
use Invoke\Pipes\ClassPipe;
use Invoke\Utils;
use Invoke\Utils\ReflectionUtils;
use Invoke\Validations\ArrayOf;
use ReflectionClass;
use ReflectionException;

class TypeDocument extends Data
{
    public string $name;

    public ?string $summary;

    public ?string $description;

    public bool $isBuiltin;

    public bool $isData;

    public bool $isUnion;

    public bool $isFile;

    #[ArrayOf(TypeDocument::class)]
    public array $unionTypes;

    #[ArrayOf(ParamDocument::class)]
    public array $params;

    /**
     * @throws ReflectionException
     */
    public function render(Pipe $type): array
    {
        $name = $type->getTypeName();

        if ($type instanceof ClassPipe) {
            $type = $type->class;
        }

        $isBuiltin = Utils::isPipeTypeBuiltin($type);
        $isData = Utils::isPipeTypeData($type);
        $isUnion = Utils::isPipeTypeUnion($type);
        $isFile = Utils::isPipeTypeFile($type);

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
            "summary" => $summary,
            "description" => $description,

            "isBuiltin" => $isBuiltin,
            "isData" => $isData,
            "isUnion" => $isUnion,
            "isFile" => $isFile,

            "unionTypes" => TypeDocument::many($unionTypes),
            "params" => ParamDocument::many($params),
        ];
    }
}