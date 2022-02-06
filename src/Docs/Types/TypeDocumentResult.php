<?php
//
//namespace Invoke\Docs\Types;
//
//use Invoke\Types;
//use Invoke\Typesystem;
//use Invoke\Typesystemx\CustomType;
//use Invoke\Typesystemx\GenericCustomType;
//use Invoke\Typesystemx\Result;
//use Invoke\Typesystemx\Type;
//use Invoke\Utils\ReflectionUtils;
//use ReflectionClass;
//
//class TypeDocumentResult extends Result
//{
//    /**
//     * @var mixed $type
//     */
//    private $type;
//
//    /**
//     * @var string $class
//     */
//    public ?string $class;
//
//    /**
//     * @var string $name
//     */
//    public string $name;
//
//    /**
//     * @var string $as_string
//     */
//    public string $as_string;
//
//    /**
//     * @var string $some_types
//     */
//    public ?array $some_types;
//
//    /**
//     * @var TypeDocumentResult[] $generics
//     */
//    public ?array $generics;
//
//    /**
//     * @var string|null $summary
//     */
//    public ?string $summary;
//
//    /**
//     * @var string|null $description
//     */
//    public ?string $description;
//
//    /**
//     * @var ParamDocumentResult[] $params
//     */
//    public ?array $params;
//
//    /**
//     * @return array
//     */
//    public static function params(): array
//    {
//        return [
//            "generics" => Types::Null(Types::ArrayOf(TypeDocumentResult::class)),
//            "params" => Types::Null(Types::ArrayOf(ParamDocumentResult::class)),
//        ];
//    }
//
//    /**
//     * @param $type
//     * @return static|null
//     */
//    public static function createFromInvokeType($type): ?self
//    {
//        $comment = static::createComment($type);
//
//        $params = null;
//        if (!Typesystem::isBuiltinType($type) && !($type instanceof CustomType) && !is_array($type)) {
//            $params = [];
//
//            if (is_string($type) && class_exists($type)) {
//                $reflectionClass = new ReflectionClass($type);
//
//                $typeParams = ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionClass);
//
//                $params = [];
//                foreach ($typeParams as $paramName => $paramType) {
//                    $params[] = ParamDocumentResult::createFromNameAndType($paramName, $paramType);
//                }
//            }
//        }
//
//        $generics = null;
//        if ($type instanceof GenericCustomType) {
//            $generics = array_map(fn($type) => TypeDocumentResult::createFromInvokeType($type), $type->getGenericTypes());
//        }
//
//        $result = static::from([
//            "class" => is_string($type) && class_exists($type) ? $type : null,
//            "name" => Typesystem::getTypeName($type),
//            "as_string" => Typesystem::getTypeName($type),
//            "some_types" => is_array($type) ? array_map(fn($type) => static::createFromInvokeType($type), $type) : null,
//            "summary" => $comment["summary"],
//            "description" => $comment["description"],
//            "params" => $params,
//            "generics" => $generics,
//        ]);
//
//        $result->setType($type);
//
//        return $result;
//    }
//
//    /**
//     * @param $type
//     * @return array
//     */
//    protected static function createComment($type): array
//    {
//        if ($type instanceof CustomType || (is_string($type) && is_subclass_of($type, Type::class))) {
//            $reflectionClass = new ReflectionClass($type);
//
//            return ReflectionUtils::parseComment($reflectionClass);
//        }
//
//        $comment = [
//            "summary" => null,
//            "description" => null,
//        ];
//
//        switch ($type) {
//            case "string":
//                $comment["summary"] = "A string value.";
//                break;
//            case "int":
//                $comment["summary"] = "An integer value.";
//                break;
//            case "float":
//                $comment["summary"] = "A float value.";
//                break;
//            case "bool":
//                $comment["summary"] = "A boolean value.";
//                break;
//            case "array":
//                $comment["summary"] = "An array.";
//                break;
//        }
//
//        return $comment;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getType()
//    {
//        return $this->type;
//    }
//
//    /**
//     * todo: fix this thing
//     *
//     * @param mixed $type
//     */
//    public function setType($type): void
//    {
//        $this->type = $type;
//    }
//}
