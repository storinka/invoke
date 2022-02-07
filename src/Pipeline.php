<?php

namespace Invoke;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\ParamInvalidTypeException;
use Invoke\Exceptions\ParamTypeNameRequiredException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\Types\WrappedType;
use RuntimeException;

/**
 * TODO: describe it
 */
class Pipeline
{
    public static function pass(Pipe|string $pipe, mixed $value = null)
    {
        if ($pipe instanceof Pipe) {
            return $pipe->pass($value);
        }

        if (class_exists($pipe)) {
            if (is_subclass_of($pipe, Singleton::class)) {
                return $pipe::getInstance()->pass($value);
            }

            if (is_subclass_of($pipe, Type::class)) {
                return (new WrappedType($pipe))->pass($value);
            }

            return Container::make($pipe)->pass($value);
        }

        throw new RuntimeException("Invalid pipe: $pipe");
    }

    public static function catcher(callable $callback, string $prefix)
    {
        try {
            return $callback();
        } catch (ParamInvalidTypeException $exception) {
            throw new ParamInvalidTypeException(
                "{$prefix}::{$exception->path}",
                $exception->expectedType,
                $exception->value,
            );
        } catch (InvalidTypeException $exception) {
            throw new ParamInvalidTypeException(
                "{$prefix}",
                $exception->expectedType,
                $exception->value,
            );
        } catch (ParamTypeNameRequiredException $exception) {
            throw new ParamTypeNameRequiredException(
                "{$prefix}::{$exception->path}",
            );
        } catch (TypeNameRequiredException $exception) {
            throw new ParamTypeNameRequiredException(
                "{$prefix}",
            );
        }
    }
}