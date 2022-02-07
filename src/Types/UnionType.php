<?php

namespace Invoke\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\RequiredParamNotProvidedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\HasDynamicName;
use Invoke\HasUsedTypes;
use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;
use function invoke_is_assoc;

class UnionType implements Type, HasDynamicName, HasUsedTypes
{
    public array $pipes;

    public int $paramsPipesCount = 0;

    public function __construct(array $pipes)
    {
        $this->pipes = array_map(function ($pipe) {
            if ($pipe instanceof TypeWithParams) {
                $this->paramsPipesCount++;
            }

            if ($pipe instanceof WrappedType) {
                if (is_subclass_of($pipe->typeClass, TypeWithParams::class)) {
                    $this->paramsPipesCount++;
                }
            }

            if (is_string($pipe)) {
                if (class_exists($pipe)) {
                    if (is_subclass_of($pipe, TypeWithParams::class)) {
                        $this->paramsPipesCount++;
                    }

                    if (is_subclass_of($pipe, Singleton::class)) {
                        return $pipe::getInstance();
                    }

                    return new WrappedType($pipe);
                } else {
                    return Utils::typeNameToPipe($pipe);
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
                if (invoke_is_assoc($value)) {
                    if (!array_key_exists("@type", $value)) {
                        throw new TypeNameRequiredException();
                    } else {
                        $valueType = $value["@type"];

                        foreach ($this->pipes as $pipe) {
                            if (Utils::getPipeTypeName($pipe) === $valueType) {
                                return Pipeline::pass($pipe, $value);
                            }
                        }
                    }
                }
            }
        }

        foreach ($this->pipes as $pipe) {
            try {
                return Pipeline::pass($pipe, $value);
            } catch (RequiredParamNotProvidedException|InvalidTypeException $exception) {
//                if ($exception->expectedType !== $pipe) {
//                    throw new InvalidTypeException($pipe, $value);
//                }
            }
        }

        throw new InvalidTypeException($this, $value);
    }

    public function getDynamicName(): string
    {
        return implode(
            " | ",
            array_map(fn(Pipe $pipe) => Utils::getPipeTypeName($pipe), $this->pipes)
        );
    }

    public static function getName(): string
    {
        return "union";
    }

    public function getUsedTypes(): array
    {
        return $this->pipes;
    }
}