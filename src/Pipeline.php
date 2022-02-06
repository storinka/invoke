<?php

namespace Invoke;

use Invoke\Exceptions\ParamTypeNameRequiredException;
use Invoke\Exceptions\ParamValidationFailedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Pipes\ClassPipe;
use RuntimeException;

class Pipeline
{
    public static function make(Pipe|string $pipe, mixed $value)
    {
        if ($pipe instanceof Pipe) {
            return $pipe->pass($value);
        }

        if (class_exists($pipe)) {
            if (is_subclass_of($pipe, PipeSingleton::class)) {
                return $pipe::getInstance()->pass($value);
            }

            return (new ClassPipe($pipe))->pass($value);
        }

        throw new RuntimeException();
    }

    public static function getValueTypeName(mixed $value): string
    {
        if ($value instanceof Pipe) {
            return $value->getTypeName();
        }

        return gettype($value);
    }

    public static function catcher(callable $callback, string $prefix)
    {
        try {
            return $callback();
        } catch (ParamValidationFailedException $exception) {
            throw new ParamValidationFailedException(
                "{$prefix}::{$exception->path}",
                $exception->pipe,
                $exception->value,
            );
        } catch (ValidationFailedException $exception) {
            throw new ParamValidationFailedException(
                "{$prefix}",
                $exception->pipe,
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