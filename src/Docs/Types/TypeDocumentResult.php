<?php

namespace Invoke\Docs\Types;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\GenericCustomType;
use Invoke\Typesystem\Result;
use Invoke\Typesystem\Type;
use Invoke\Typesystem\Types;
use Invoke\Typesystem\Typesystem;
use Invoke\Typesystem\Utils\ReflectionUtils;
use ReflectionClass;

class TypeDocumentResult extends Result
{
    /**
     * @var mixed $type
     */
    public $type;

    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var TypeDocumentResult[] $generics
     */
    public ?array $generics;

    /**
     * @var string|null $summary
     */
    public ?string $summary;

    /**
     * @var string|null $description
     */
    public ?string $description;

    /**
     * @var ParamDocumentResult[] $params
     */
    public ?array $params;

    /**
     * @return array
     */
    public static function params(): array
    {
        return [
            "generics" => Types::Null(Types::ArrayOf(TypeDocumentResult::class)),
            "params" => Types::Null(Types::ArrayOf(ParamDocumentResult::class)),
        ];
    }

    /**
     * @param $type
     * @return static|null
     */
    public static function createFromInvokeType($type): ?self
    {
        $comment = static::createComment($type);

        $params = null;
        if (!Typesystem::isSimpleType($type) && !($type instanceof CustomType) && !is_array($type)) {
            $params = [];

            if (is_string($type) && class_exists($type)) {
                $reflectionClass = new ReflectionClass($type);

                $typeParams = ReflectionUtils::inspectInvokeTypeReflectionClassParams($reflectionClass);

                $params = [];
                foreach ($typeParams as $paramName => $paramType) {
                    $params[] = ParamDocumentResult::createFromNameAndType($paramName, $paramType);
                }
            }
        }

        $generics = null;
        if ($type instanceof GenericCustomType) {
            $generics = array_map(fn($type) => TypeDocumentResult::createFromInvokeType($type), $type->getGenericTypes());
        }

        return static::from([
            "type" => $type,

            "name" => Typesystem::getTypeName($type),
            "summary" => $comment["summary"],
            "description" => $comment["description"],
            "params" => $params,
            "generics" => $generics,
        ]);
    }

    /**
     * @param $type
     * @return array
     */
    protected static function createComment($type): array
    {
        if ($type instanceof CustomType || (is_string($type) && is_subclass_of($type, Type::class))) {
            $reflectionClass = new ReflectionClass($type);

            return ReflectionUtils::parseComment($reflectionClass);
        }

        $comment = [
            "summary" => null,
            "description" => null,
        ];

        switch ($type) {
            case Types::String:
                $comment["summary"] = "A string value.";
                break;
            case Types::Int:
                $comment["summary"] = "An integer value.";
                break;
            case Types::Float:
                $comment["summary"] = "A float value.";
                break;
            case Types::Bool:
                $comment["summary"] = "A boolean value.";
                break;
            case Types::Array:
                $comment["summary"] = "An array.";
                break;
        }

        return $comment;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
