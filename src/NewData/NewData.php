<?php

namespace Invoke\NewData;

use Invoke\Attributes\NotParameter;
use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\NewMethod\Information\ParameterInformationInterface;
use Invoke\Piping;
use Invoke\Stop;
use Invoke\Utils\Utils;
use Invoke\Utils\Validation;
use RuntimeException;

class NewData implements NewTypeInterface
{
    use NewDataHelpers;

    /**
     * @var bool $isPassed
     */
    #[NotParameter]
    protected bool $isPassed = false;

    public function pass(mixed $input): mixed
    {
        if ($this->isPassed) {
            throw new RuntimeException("Passing data through \"" . static::class . "\" second time is forbidden.");
        }

        $this->isPassed = true;

        if ($input instanceof Stop) {
            return $input;
        }

        // get builtin input type
        $inputBuiltinType = gettype($input);

        // type with params allows only objects and arrays as input
        if ($inputBuiltinType !== "object" && $inputBuiltinType !== "array") {
            throw new InvalidTypeException($this, Utils::getValueTypeName($input));
        }

        $overridden = [];

        // check if parameter is overridden by "override" method
        if (method_exists($this, "override")) {
            $overridden = $this->override($input);
        }

        if (is_array($input)) {
            $inputTypeName = $input["@type"] ?? null;

            if ($inputTypeName) {
                if ($inputTypeName !== Utils::getPipeTypeName($this::class)) {
                    throw new InvalidTypeException($this, $inputTypeName);
                }
            }
        }

        $parametersInformation = $this->asInvokeGetParametersInformation();
        $parametersInformation = array_filter(
            $parametersInformation,
            fn(ParameterInformationInterface $parameterInformation) => !array_key_exists($parameterInformation->getName(), $overridden)
        );

        $parameters = Validation::validateParametersInformation(
            $parametersInformation,
            $input
        );

        $parameters = array_merge($parameters, $overridden);

        foreach ($parameters as $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }

    /**
     * @param mixed $input
     * @return static
     */
    public static function from(mixed $input): static
    {
        $instance = Container::make(static::class);

        return Piping::run($instance, $input);
    }

    /**
     * @param mixed $input
     * @param string $mapFn
     * @return ?static
     */
    public static function nullable(mixed $input, string $mapFn = "from"): ?static
    {
        if ($input === null) {
            return null;
        }

        return static::{$mapFn}($input);
    }

    /**
     * @param iterable $items
     * @param string $mapFn
     * @return static[]
     */
    public static function many(iterable $items, string $mapFn = "from"): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = static::{$mapFn}($item);
        }

        return $result;
    }
}