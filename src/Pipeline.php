<?php

namespace Invoke;

use Ds\Map;
use Ds\Set;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\ParamInvalidTypeException;
use Invoke\Exceptions\ParamTypeNameRequiredException;
use Invoke\Exceptions\ParamValidationFailedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Support\Singleton;
use Invoke\Types\WrappedType;
use RuntimeException;

/**
 * TODO: describe it
 */
final class Pipeline
{
    protected static Map $before;
    protected static Map $after;
    public static Map $override;

    public static function override(Pipe|string $oldPipe, Pipe|string $newPipe): void
    {
        if (!isset(Pipeline::$override)) {
            Pipeline::$override = new Map();
        }

        Pipeline::$override->put($oldPipe, $newPipe);
    }

    public static function removeOverride(Pipe|string $oldPipe): void
    {
        if (!isset(Pipeline::$override)) {
            Pipeline::$override = new Map();
        }

        Pipeline::$override->remove($oldPipe);
    }

    public static function before(Pipe|string $pipe, Pipe|string $beforePipe): void
    {
        Pipeline::fillBefore($pipe);

        Pipeline::$before->get($pipe)->add($beforePipe);
    }

    public static function removeBefore(Pipe|string $pipe, Pipe|string $beforePipe): void
    {
        Pipeline::fillBefore($pipe);

        Pipeline::$before->get($pipe)->remove($beforePipe);
    }

    public static function after(Pipe|string $pipe, Pipe|string $afterPipe): void
    {
        Pipeline::fillAfter($pipe);

        Pipeline::$after->get($pipe)->add($afterPipe);
    }

    public static function removeAfter(Pipe|string $pipe, Pipe|string $afterPipe): void
    {
        Pipeline::fillBefore($pipe);

        Pipeline::$after->get($pipe)->remove($afterPipe);
    }

    public static function pass(Pipe|string|array $pipe, mixed $value = null): mixed
    {
        // if pipe is array, run all pipes inside it
        if (is_array($pipe)) {
            foreach ($pipe as $pipeOfPipeline) {
                $value = Pipeline::pass($pipeOfPipeline, $value);
            }

            return $value;
        }

        // if pipe is Stop, return it
        if ($pipe instanceof Stop) {
            return $pipe;
        }

        $pipeClass = is_string($pipe) ? $pipe : $pipe::class;

        // check if pipe was overridden
        if (isset(Pipeline::$override)) {
            $pipe = Pipeline::$override->get($pipeClass, $pipe);
        }

        // run before pipes
        if (isset(Pipeline::$before)) {
            $beforePipes = Pipeline::$before->get($pipeClass, []);

            foreach ($beforePipes as $beforePipe) {
                $value = Pipeline::pass($beforePipe, $value);
            }
        }

        $validPipe = false;

        if ($pipe instanceof Pipe) {
            $validPipe = true;

            $value = $pipe->pass($value);
        } else if (class_exists($pipe) && is_subclass_of($pipe, Pipe::class)) {
            $validPipe = true;

            if (is_subclass_of($pipe, Singleton::class)) {
                $value = $pipe::getInstance()->pass($value);
            } else if (is_subclass_of($pipe, Type::class)) {
                $value = (new WrappedType($pipe))->pass($value);
            } else {
                $value = Container::make($pipe)->pass($value);
            }
        }

        if ($validPipe) {
            // run after pipes
            if (isset(Pipeline::$after)) {
                $afterPipes = Pipeline::$after->get($pipeClass, []);

                foreach ($afterPipes as $afterPipe) {
                    $value = Pipeline::pass($afterPipe, $value);
                }
            }

            return $value;
        }

        throw new RuntimeException("Invalid pipe: $pipe");
    }

    // todo: move somewhere else
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
        } catch (ParamValidationFailedException $exception) {
            throw new ParamValidationFailedException(
                "{$prefix}::{$exception->path}",
                $exception->getMessage()
            );
        } catch (ValidationFailedException $exception) {
            throw new ParamValidationFailedException(
                "{$prefix}",
                $exception->getMessage()
            );
        }
    }

    protected static function fillBefore(Pipe|string $pipe): void
    {
        if (!isset(Pipeline::$before)) {
            Pipeline::$before = new Map();
        }

        if (!Pipeline::$before->hasKey($pipe)) {
            Pipeline::$before->put($pipe, new Set());
        }
    }

    protected static function fillAfter(Pipe|string $pipe): void
    {
        if (!isset(Pipeline::$after)) {
            Pipeline::$after = new Map();
        }

        if (!Pipeline::$after->hasKey($pipe)) {
            Pipeline::$after->put($pipe, new Set());
        }
    }
}