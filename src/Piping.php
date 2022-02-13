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
use Invoke\Meta\Singleton;
use Invoke\Types\WrappedType;
use RuntimeException;

/**
 * Invoke piping system.
 */
final class Piping
{
    /**
     * @var Map<string, Set<Pipe|string>> $before
     */
    protected static Map $before;

    /**
     * @var Map<string, Set<Pipe|string>> $after
     */
    protected static Map $after;

    /**
     * @var Map<string, Pipe|string> $replaced
     */
    protected static Map $replaced;

    /**
     * Run some value trough a pipe.
     *
     * @param Pipe|string|array $pipe
     * @param mixed|null $value
     * @return mixed
     */
    public static function run(Pipe|string|array $pipe, mixed $value = null): mixed
    {
        // if pipe is array, run all pipes inside it
        if (is_array($pipe)) {
            return Piping::runArray($pipe, $value);
        }

        // if pipe is Stop, return it
        if ($pipe instanceof Stop) {
            return $pipe;
        }

        $pipeClass = is_string($pipe) ? $pipe : $pipe::class;

        // check if pipe was replaced
        if (isset(Piping::$replaced)) {
            $pipe = Piping::$replaced->get($pipeClass, $pipe);
        }

        // run before pipes
        $value = Piping::runBeforePipes($pipeClass, $value);

        if ($pipe instanceof Pipe) {
            $value = $pipe->pass($value);
        } elseif (class_exists($pipe) && is_subclass_of($pipe, Pipe::class)) {
            $value = Piping::runClass($pipe, $value);
        } else {
            throw new RuntimeException("Invalid pipe: $pipe");
        }

        // run after pipes
        return Piping::runAfterPipes($pipeClass, $value);
    }

    /**
     * Replace some pipe.
     *
     * @param string $oldPipe
     * @param Pipe|string $newPipe
     * @return void
     */
    public static function replace(string $oldPipe, Pipe|string $newPipe): void
    {
        if (!isset(Piping::$replaced)) {
            Piping::$replaced = new Map();
        }

        Piping::$replaced->put($oldPipe, $newPipe);
    }

    /**
     * Return replaced pipe.
     *
     * @param string $oldPipe
     * @return void
     */
    public static function return(string $oldPipe): void
    {
        if (!isset(Piping::$replaced)) {
            Piping::$replaced = new Map();
        }

        Piping::$replaced->remove($oldPipe);
    }

    /**
     * Insert a pipe before another pipe.
     *
     * @param string $pipe
     * @param Pipe|string $beforePipe
     * @return void
     */
    public static function before(string $pipe, Pipe|string $beforePipe): void
    {
        Piping::prepareBefore($pipe);

        Piping::$before->get($pipe)->add($beforePipe);
    }

    /**
     * Removed inserted before pipe.
     *
     * @param string $pipe
     * @param Pipe|string $beforePipe
     * @return void
     */
    public static function removeBefore(string $pipe, Pipe|string $beforePipe): void
    {
        Piping::prepareBefore($pipe);

        Piping::$before->get($pipe)->remove($beforePipe);
    }

    /**
     * Insert a pipe after another pipe.
     *
     * @param string $pipe
     * @param Pipe|string $afterPipe
     * @return void
     */
    public static function after(string $pipe, Pipe|string $afterPipe): void
    {
        Piping::prepareAfter($pipe);

        Piping::$after->get($pipe)->add($afterPipe);
    }

    /**
     * Remove inserted after pipe.
     *
     * @param string $pipe
     * @param Pipe|string $afterPipe
     * @return void
     */
    public static function removeAfter(string $pipe, Pipe|string $afterPipe): void
    {
        Piping::prepareBefore($pipe);

        Piping::$after->get($pipe)->remove($afterPipe);
    }

    /**
     * @internal
     */
    protected static function prepareBefore(Pipe|string $pipe): void
    {
        if (!isset(Piping::$before)) {
            Piping::$before = new Map();
        }

        if (!Piping::$before->hasKey($pipe)) {
            Piping::$before->put($pipe, new Set());
        }
    }

    /**
     * @internal
     */
    protected static function prepareAfter(Pipe|string $pipe): void
    {
        if (!isset(Piping::$after)) {
            Piping::$after = new Map();
        }

        if (!Piping::$after->hasKey($pipe)) {
            Piping::$after->put($pipe, new Set());
        }
    }

    /**
     * @internal
     */
    protected static function runBeforePipes(string $pipeClass, mixed $value): mixed
    {
        if (isset(Piping::$before)) {
            $beforePipes = Piping::$before->get($pipeClass, []);

            foreach ($beforePipes as $beforePipe) {
                $value = Piping::run($beforePipe, $value);
            }
        }

        return $value;
    }

    /**
     * @internal
     */
    protected static function runAfterPipes(string $pipeClass, mixed $value): mixed
    {
        if (isset(Piping::$after)) {
            $afterPipes = Piping::$after->get($pipeClass, []);

            foreach ($afterPipes as $afterPipe) {
                $value = Piping::run($afterPipe, $value);
            }
        }

        return $value;
    }

    /**
     * @internal
     */
    protected static function runArray(array $pipes, mixed $value): mixed
    {
        foreach ($pipes as $pipe) {
            $value = Piping::run($pipe, $value);
        }

        return $value;
    }

    /**
     * @internal
     */
    protected static function runClass(string $class, mixed $value): mixed
    {
        if (is_subclass_of($class, Singleton::class)) {
            return $class::getInstance()->pass($value);
        }

        if (Container::has($class)) {
            return Container::get($class)->pass($value);
        }

        if (is_subclass_of($class, Type::class)) {
            return (new WrappedType($class))->pass($value);
        }

        return Container::make($class)->pass($value);
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
}
