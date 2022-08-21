<?php

namespace Invoke\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\NewMethod\Information\HasParametersInformation;
use Invoke\Pipe;
use Invoke\Piping;
use Invoke\Support\HasDynamicTypeName;
use Invoke\Support\HasUsedTypes;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;
use function Invoke\Utils\is_assoc;

class UnionType implements Type, HasDynamicTypeName, HasUsedTypes
{
    public array $pipes;

    public int $paramsPipesCount = 0;

    public bool $allowArrays = false;

    public function __construct(array $pipes)
    {
        $this->pipes = array_map(function ($pipe) {
            if ($pipe instanceof HasParametersInformation) {
                $this->paramsPipesCount++;
            }

            if ($pipe instanceof WrappedType) {
                if (is_subclass_of($pipe->typeClass, HasParametersInformation::class)) {
                    $this->paramsPipesCount++;
                }
            }

            $type = $pipe;

            if (is_string($pipe)) {
                if (class_exists($pipe)) {
                    if (is_subclass_of($pipe, HasParametersInformation::class)) {
                        $this->paramsPipesCount++;
                    }

                    if (is_subclass_of($pipe, Singleton::class)) {
                        $type = $pipe::getInstance();
                    } else {
                        $type = new WrappedType($pipe);
                    }
                } else {
                    $type = Utils::typeNameToPipe($pipe);
                }
            }

            if ($pipe instanceof ArrayType) {
                $this->allowArrays = true;
            }

            return $type;
        }, $pipes);
    }

    public function run(mixed $value): mixed
    {
        if ($this->paramsPipesCount > 1) {
            if (is_array($value)) {
                if (!$this->allowArrays || is_assoc($value)) {
                    if (!array_key_exists("@type", $value)) {
                        throw new TypeNameRequiredException();
                    } else {
                        $valueType = $value["@type"];

                        if (Utils::isTypeNameBuiltin($valueType)) {
                            throw new TypeNameRequiredException();
                        }

                        foreach ($this->pipes as $pipe) {
                            if (Utils::getPipeTypeName($pipe) === $valueType) {
                                return Piping::run($pipe, $value);
                            }
                        }
                    }
                }
            }
        }

        foreach ($this->pipes as $pipe) {
            try {
                return Piping::run($pipe, $value);
            } catch (RequiredParameterNotProvidedException|TypeNameRequiredException|InvalidTypeException) {
            }
        }

        throw new InvalidTypeException($this, $valueType ?? Utils::getValueTypeName($value));
    }

    public function invoke_getDynamicTypeName(): string
    {
        return implode(
            " | ",
            array_map(fn(Pipe $pipe) => Utils::getPipeTypeName($pipe), $this->pipes)
        );
    }

    public static function invoke_getTypeName(): string
    {
        return "union";
    }

    public function invoke_getUsedTypes(): array
    {
        return $this->pipes;
    }
}
