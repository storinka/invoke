<?php

namespace Invoke\V1\Docs\Types;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\Type;
use Invoke\V1\Typesystem\ResultV1;
use Invoke\V1\Typesystem\Types;
use Invoke\V1\Typesystem\TypesystemV1;
use Invoke\V1\Typesystem\Utils\ReflectionUtils;
use ReflectionClass;

class TypeDocumentResult extends ResultV1
{
    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string|null $summary
     */
    public ?string $summary;

    /**
     * @var string|null $description
     */
    public ?string $description;

    /**
     * @var TypeDocumentResult[] $params
     */
    public array $params;

    /**
     * @return array
     */
    public static function params(): array
    {
        return [
            "params" => Types::ArrayOf(TypeDocumentResult::class),
        ];
    }

    /**
     * @param $type
     * @return static|null
     */
    public static function createFromInvokeType($type): ?self
    {
        $comment = static::createComment($type);

        if (is_string($type) && class_exists($type)) {
            $reflectionClass = new ReflectionClass($type);

            $params = ReflectionUtils::inspectInvokeTypeReflectionClassParams($reflectionClass);
        }

        return static::create([
            "name" => TypesystemV1::getTypeName($type),
            "summary" => $comment["summary"],
            "description" => $comment["description"],
            "params" => [],
        ]);
    }

    /**
     * @param $type
     * @return array
     */
    protected static function createComment($type): array
    {
        if ($type instanceof CustomType || (is_string($type) && class_parents($type))) {
            $reflectionClass = new ReflectionClass($type);

            return ReflectionUtils::parseComment($reflectionClass);
        }

        $comment = [
            "summary" => null,
            "description" => null,
        ];

        switch ($type) {
            case Type::String:
                $comment["summary"] = "A string value.";
                break;
            case Type::Int:
                $comment["summary"] = "An integer value.";
                break;
            case Type::Float:
                $comment["summary"] = "A float value.";
                break;
            case Type::Bool:
                $comment["summary"] = "A boolean value.";
                break;
            case Type::Array:
                $comment["summary"] = "An array.";
                break;
        }

        return $comment;
    }
}
