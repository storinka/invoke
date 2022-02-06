<?php

namespace Invoke;

use Exception;
use Invoke\Pipes\ArrayPipe;
use Invoke\Pipes\BoolPipe;
use Invoke\Pipes\FloatPipe;
use Invoke\Pipes\IntPipe;
use Invoke\Pipes\NullPipe;
use Invoke\Pipes\StringPipe;
use Invoke\Pipes\UnionPipe;
use Invoke\Types\File;
use function invoke_get_class_name;

class Utils
{
    public static function getMethodNameFromClass(string $class): string
    {
        $name = invoke_get_class_name($class);

        $first = mb_strtolower($name[0]);
        $rest = substr($name, 1);

        return "$first$rest";
    }

    public static function getErrorNameFromException(Exception|string $exception): string
    {
        if (is_string($exception)) {
            $className = $exception;
        } else {
            $className = $exception::class;
        }

        $className = invoke_get_class_name($className);

        if (str_ends_with($className, "Exception")) {
            $className = substr($className, 0, strlen($className) - 9);
        }

        $name = static::camelToUnderscore($className, "_");

        return strtoupper($name);
    }

    public static function camelToUnderscore($string, $us = "-"): string
    {
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $us, $string));
    }

    public static function isPipeTypeBuiltin(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return in_array(
            $class,
            [
                IntPipe::class,
                StringPipe::class,
                FloatPipe::class,
                StringPipe::class,
                NullPipe::class,
                ArrayPipe::class,
                BoolPipe::class
            ]
        );
    }

    public static function isPipeTypeData(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === Data::class || is_subclass_of($class, Data::class);
    }

    public static function isPipeTypeUnion(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === UnionPipe::class || is_subclass_of($class, UnionPipe::class);
    }

    public static function isPipeTypeFile(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === File::class || is_subclass_of($class, File::class);
    }
}