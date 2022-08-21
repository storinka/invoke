<?php

namespace Invoke;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\NewData\DataHelpers;
use Invoke\NewData\DataInterface;
use Invoke\NewMethod\Information\ParameterInformationInterface;
use Invoke\Support\WithSingleRunOrThrow;
use Invoke\Utils\Utils;
use Invoke\Utils\Validation;
use RuntimeException;
use function Invoke\Utils\get_class_name;

class Data implements DataInterface
{
    use DataHelpers,
        WithSingleRunOrThrow;

    private array $parameters = [];

    public function singleRun(mixed $input): mixed
    {
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
            $this->parameters[$name] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function from(mixed $input): static
    {
        $instance = Container::make(static::class);

        return Piping::run($instance, $input);
    }

    /**
     * @inheritDoc
     */
    public static function nullable(mixed $input, string $mapFn = "from"): ?static
    {
        if ($input === null) {
            return null;
        }

        return static::{$mapFn}($input);
    }

    /**
     * @inheritDoc
     */
    public static function many(iterable $items, string $mapFn = "from"): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = static::{$mapFn}($item);
        }

        return $result;
    }

    /**
     * @return string
     */
    public static function invoke_getTypeName(): string
    {
        return get_class_name(static::class);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->getParameters();
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException("Not implemented.");
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}