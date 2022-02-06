<?php

namespace Invoke\Pipes;

use Invoke\AbstractPipe;
use Invoke\Container;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Method;
use Invoke\Pipe;
use Invoke\Utils\ReflectionUtils;
use Invoke\Validator;
use ReflectionClass;
use function invoke_get_class_name;

class ClassPipe extends AbstractPipe
{
    public string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function pass(mixed $value): mixed
    {
        if (is_object($value)) {
            if ($value::class === $this->class) {
                return $value;
            }
        }

        if (is_subclass_of($this->class, Pipe::class)) {
            $newPipe = Container::make($this->class);

            return $newPipe->pass($value);
        }

        throw new ValidationFailedException($this, $value);
    }

    public function getTypeName(): string
    {
        return invoke_get_class_name($this->class);
    }

    public function getUsedPipes(): array
    {
        $pipes = [];

        if (is_subclass_of($this->class, ParamsPipe::class)) {
            return ReflectionUtils::extractPipesFromParamsPipe($this->class);
        }

        return $pipes;
    }
}