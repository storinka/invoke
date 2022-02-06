<?php

namespace Invoke\Pipes;

use Invoke\AbstractPipe;
use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\RequiredParamNotProvidedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Utils\ReflectionUtils;

class UnionPipe extends AbstractPipe
{
    public array $pipes;

    public int $paramsPipesCount = 0;

    public function __construct(array $pipes)
    {
        $this->pipes = array_map(function ($pipe) {
            if ($pipe instanceof ParamsPipe) {
                $this->paramsPipesCount++;
            }

            if ($pipe instanceof ClassPipe) {
                if (is_subclass_of($pipe->class, ParamsPipe::class)) {
                    $this->paramsPipesCount++;
                }
            }

            if (is_string($pipe)) {
                if (class_exists($pipe)) {
                    if (is_subclass_of($pipe, ParamsPipe::class)) {
                        $this->paramsPipesCount++;
                    }

                    if (is_subclass_of($pipe, AbstractSingletonPipe::class)) {
                        return $pipe::getInstance();
                    }

                    return new ClassPipe($pipe);
                } else {
                    return ReflectionUtils::typeToPipe($pipe);
                }
            } else {
                return $pipe;
            }
        }, $pipes);
    }

    public function pass(mixed $value): mixed
    {
        if ($this->paramsPipesCount > 1) {
            if (is_array($value)) {
                if (!array_key_exists("@type", $value)) {
                    throw new TypeNameRequiredException();
                } else {
                    $valueType = $value["@type"];

                    foreach ($this->pipes as $pipe) {
                        if ($pipe->getTypeName() === $valueType) {
                            return Pipeline::make($pipe, $value);
                        }
                    }
                }
            }
        }

        foreach ($this->pipes as $pipe) {
            try {
                return Pipeline::make($pipe, $value);
            } catch (RequiredParamNotProvidedException|ValidationFailedException) {
                // ignore
            }
        }

        throw new ValidationFailedException($this, $value);
    }

    public function getTypeName(): string
    {
        return implode(
            " | ",
            array_map(fn(Pipe $pipe) => $pipe->getTypeName(), $this->pipes)
        );
    }
}