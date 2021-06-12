<?php

namespace Invoke\Typescript;

use Invoke\Typesystem\CustomType;
use Invoke\Typesystem\CustomTypes\TypedArrayCustomType;
use Invoke\Typesystem\Result;
use Invoke\Typesystem\Type;
use Invoke\Typesystem\Undef;
use ReflectionClass;

function str_starts_with(string $str, string $with): bool
{
    return substr($str, 0, strlen($with)) === $with;
}

function str_ends_with(string $str, string $with): bool
{
    return substr($str, strlen($str) - strlen($with)) === $with;
}

class Typescript
{
    private static function getType($type): string
    {
        if ($type instanceof CustomType) {
            if ($type instanceof TypedArrayCustomType) {
                return "Array<" . Typescript::getType($type->getItemType()) . ">";
            }

            return Typescript::getType($type->getType());
        }

        if (is_array($type)) {
            return implode(" | ", array_map(fn($t) => Typescript::getType($t), $type));
        }

        if ($type instanceof Undef) {
            return "undefined";
        }

        switch ($type) {
            case "int":
            case Type::Int:
            case "float":
            case Type::Float:
                return "number";

            case Type::String:
                return "string";

            case Type::Array:
                return "Array<any>";

            case "bool":
            case Type::Bool:
                return "boolean";

            case null:
            case Type::Null:
                return "null";

            case Type::Undef:
                return "undefined";

            case Type::Map:
            case Type::T:
                return "any";
        }

        if (is_string($type) && class_exists($type)) {
            return invoke_get_class_name($type);
        }

        return "any";
    }

    private static function mapParams(array $params): array
    {
        $newParams = [];
        foreach ($params as $paramName => $paramType) {
            $newParams[] = [
                "name" => $paramName,
                "type" => Typescript::getType($paramType),
            ];
        }
        return $newParams;
    }

    private static function mapProps(array $props): array
    {
        $newProps = [];
        foreach ($props as $propName => $propValue) {
            $newProps[] = [
                "name" => $propName,
                "value" => $propValue,
            ];
        }
        return $newProps;
    }

    private static function isParamsOptional(array $params): bool
    {
        foreach ($params as $param) {
            $type = $param["type"];

            if (!str_starts_with($type, "null |")) {
                return false;
            }
        }

        return true;
    }

    private static function renderParams(array $params, bool $multiline = true): string
    {
        $separator = $multiline ? "\n" : " ";
        $separator_next = $multiline ? "  " : "";

        return array_reduce($params, function ($a, $b) use ($separator, $separator_next) {
            $name = $b['name'];
            $type = $b['type'];

            if (str_starts_with($type, "null |")) {
                $name .= "?";
            }

            return "{$a}{$separator_next}{$name}: {$type};{$separator}";
        }, $separator);
    }

    private static function renderProps(array $params, bool $multiline = true): string
    {
        $separator = $multiline ? "\n" : " ";
        $separator_next = $multiline ? "  " : "";

        return array_reduce($params, function ($a, $b) use ($multiline, $separator, $separator_next) {
            $value = $b['value'];
            $value = is_array($value) ? Typescript::renderObject(null, $value) : $value;
            return "{$a}{$separator_next}{$b['name']}: {$value},{$separator}";
        }, $separator);
    }

    private static function renderInterface(string $name, array $params): string
    {
        $params = Typescript::renderParams(Typescript::mapParams($params));

        $s = "interface {$name} {";
        $s .= $params;
        $s .= "}";

        return $s;
    }

    private static function renderFunction(string $name, array $params, string $type, string $body): string
    {
        $paramsOptional = Typescript::isParamsOptional(Typescript::mapParams($params));
        $params = Typescript::renderParams(Typescript::mapParams($params), false);

        $optionalSym = $paramsOptional ? "?" : "";

        $s = "function {$name}(params{$optionalSym}: {{$params}}): {$type} {";
        $s .= "\n  $body\n";
        $s .= "}";

        return $s;
    }

    private static function renderObject(?string $name, array $props): string
    {
        $props = Typescript::renderProps(Typescript::mapProps($props));

        $s = "";
        if ($name) {
            $s .= "const {$name} = ";
        }
        $s .= "{";
        $s .= $props;
        $s .= "}";

        return $s;
    }

    private static function pushToTree(array $tree, string $path, string $functionName)
    {
        $pathValues = explode(".", $path);
        $last = last($pathValues);
        unset($pathValues[sizeof($pathValues) - 1]);

        $currentTree = &$tree;
        foreach ($pathValues as $value) {
            if (!isset($currentTree[$value])) {
                $currentTree[$value] = [];
            }
            $currentTree = &$currentTree[$value];
        }
        $currentTree[$last] = $functionName;

        return $tree;
    }

    private static function getInvokeFunctionFunctionName(string $functionClass): string
    {
        if (str_ends_with($functionClass, "Function")) {
            $functionClass = substr($functionClass, 0, strlen($functionClass) - 8);
        }

        return strtolower(substr($functionClass, 0, 1)) . substr($functionClass, 1);
    }

    private static function renderTree(string $name, array $tree): string
    {
        return Typescript::renderObject($name, $tree);
    }

    private static function getInvokeFunctionResult(string $functionClass)
    {
        if (!($result = $functionClass::resultType())) {
            $reflection = new ReflectionClass($functionClass);
            $returnType = $reflection->getMethod("handle")->getReturnType();
            $result = $returnType->getName();
        }

        return $result;
    }

    private static function pushToTypesToRender(array &$ttr, $type)
    {
        if ($type instanceof Result) {
            array_push($ttr, $type::class);

            foreach ($type::params() as $pn => $pt) {
                Typescript::pushToTypesToRender($ttr, $pt);
            }
        }

        if ($type instanceof TypedArrayCustomType) {
            array_push($ttr, $type->getItemType());
        }

        if (is_string($type) && class_exists($type) && is_subclass_of($type, Result::class)) {
            array_push($ttr, $type);

            foreach ($type::params() as $pn => $pt) {
                Typescript::pushToTypesToRender($ttr, $pt);
            }
        }

        if (is_array($type)) {
            foreach ($type as $rt) {
                Typescript::pushToTypesToRender($ttr, $rt);
            }
        }
    }

    public static function renderFunctions(array $functions): string
    {
        $typesToRender = [];

        foreach ($functions as $functionName => $functionClass) {
            $result = Typescript::getInvokeFunctionResult($functionClass);
            Typescript::pushToTypesToRender($typesToRender, $result);
        }

        $renderedTypes = "";
        foreach ($typesToRender as $type) {
            $renderedTypes .= Typescript::renderInterface(invoke_get_class_name($type), $type::params());
            $renderedTypes .= "\n";
        }

        return $renderedTypes;
    }
}
