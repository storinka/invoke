<?php

namespace Invoke\Support;

use ArrayAccess;
use Invoke\AbstractType;
use Invoke\Attributes\NotParameter;
use Invoke\Container;
use Invoke\Data;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\PipeException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\Exceptions\ValidatorFailedException;
use Invoke\Invoke;
use Invoke\Pipe;
use Invoke\Piping;
use Invoke\Stop;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use JsonSerializable;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;
use function gettype;
use function is_array;
use function is_object;
use function property_exists;

/**
 * Abstract type with parameters.
 *
 * @see Data
 * @see Method
 */
abstract class TypeWithParams extends AbstractType implements HasUsedTypes, JsonSerializable, HasToArray, ArrayAccess
{
    /**
     * List of registered parameters.
     *
     * @var array $parameterNames
     */
    #[NotParameter]
    protected array $parameterNames = [];

    /**
     * @param mixed $input
     * @return static
     */
    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        $reflectionClass = ReflectionUtils::getClass($this::class);

        $parameters = $this->validate(
            $reflectionClass->getProperties(),
            $input
        );

        foreach ($parameters as $name => $value) {
            $this->set($name, $value, false);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function invoke_getUsedTypes(): array
    {
        return ReflectionUtils::extractUsedPipesFromParamsPipe($this);
    }

    /**
     * Set parameter value.
     *
     * @param string $name name pf the parameter
     * @param mixed $value value to set
     * @param bool $validate whether to run validation or not, default is "true"
     * @return void
     */
    public function set(string $name, mixed $value, bool $validate = true): void
    {
        // if validate param is true,
        // then validate the value before setting
        if ($validate) {
            $value = $this->validateParameter($name, $value);
        }

        // capitalize name
        // propertyName -> PropertyName
        $capitalizedName = ucfirst($name);

        // make setter name
        // PropertyName -> setPropertyName
        $setterName = "set{$capitalizedName}";

        // check if setter exists
        if (method_exists($this, $setterName)) {
            // use setter to set the value
            $this->{$setterName}($value);
        } else {
            // assign value to property manually
            $this->{$name} = $value;
        }
    }

    public function get(string $name): mixed
    {
        return $this->{$name};
    }

    /**
     * Validate parameter value.
     *
     * Throws {@see ValidatorFailedException} if validation failed.
     * Throws {@see RuntimeException} if "name" is not valid parameter name.
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    private function validateParameter(string $name, mixed $value): mixed
    {
        $reflectionProperty = new ReflectionProperty($this, $name);

        return $this->validateReflectionProperty($reflectionProperty, $value);
    }

    /**
     * Validate parameter value using {@see ReflectionProperty}.
     *
     * Throws {@see PipeException} if validation failed.
     * Throws {@see RuntimeException} if "name" is not valid parameter name.
     *
     * @param ReflectionProperty $reflectionProperty
     * @param mixed $value
     * @return mixed
     */
    private function validateReflectionProperty(ReflectionProperty|ReflectionParameter $reflectionProperty, mixed $value): mixed
    {
        $name = $reflectionProperty->getName();
        $type = $reflectionProperty->getType();

        if (!ReflectionUtils::isPropertyParameter($reflectionProperty)) {
            throw new PipeException("Cannot validate \"$name\" because it is not a parameter.");
        }

        $typePipe = ReflectionUtils::extractPipeFromReflectionType($type);

        if (!Utils::isPipeType($typePipe)) {
            throw new RuntimeException("Cannot validate \"$name\" because its type is not a pipe.");
        }

        $value = Piping::catcher(
            fn() => Piping::run($typePipe, $value),
            "{$name}"
        );

        // do not run attributes if value is null and is valid
        if (!($reflectionProperty instanceof ReflectionProperty ? $reflectionProperty->getType()?->allowsNull() : $reflectionProperty->allowsNull()) || $value !== null) {
            Piping::catcher(
                function () use ($name, $reflectionProperty, &$value) {
                    foreach ($reflectionProperty->getAttributes() as $attribute) {
                        if (is_subclass_of($attribute->getName(), Pipe::class)) {
                            $attributePipe = $attribute->newInstance();

                            $value = Piping::run($attributePipe, $value);
                        }
                    }
                },
                "{$name}"
            );
        }

        return $value;
    }


    /**
     * @param ReflectionProperty[]|ReflectionParameter[] $reflectionProperties
     * @param mixed $input
     * @return array
     */
    protected function validate(array $reflectionProperties, mixed $input, bool $handleInject = false): array
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

        // legacy support
        if (method_exists($this, "render")) {
            $overridden = $this->render($input);
        }

        if (is_array($input)) {
            $inputTypeName = $input["@type"] ?? null;
            if ($inputTypeName) {
                if ($inputTypeName !== Utils::getPipeTypeName($this::class)) {
                    throw new InvalidTypeException($this, $inputTypeName);
                }
            } else {
                if ($this->shouldRequireTypeName()) {
                    throw new TypeNameRequiredException();
                }
            }
        }

        if ($input instanceof AsInvokeParameterValuesOverride) {
            $overridden = array_merge($overridden, $input->toInvokeParameterValuesOverride());
        }

        if ($input instanceof AsInvokeParameterValues) {
            $input = $input->toInvokeParameterValues();
        }

        $validated = [];

        foreach ($reflectionProperties as $reflectionProperty) {
            $name = $reflectionProperty->getName();
            $type = $reflectionProperty->getType();

            // inject dependency if property with attribute #[Inject]
            if (ReflectionUtils::isPropertyDependency($reflectionProperty)) {
                if ($handleInject) {
                    $dependency = Container::get($type->getName());

                    $validated[$name] = $dependency;
                }
                continue;
            }

            // if property marked with #[NotParameter] or is private/protected/static, then skip it
            if (!ReflectionUtils::isPropertyParameter($reflectionProperty)) {
                continue;
            }

            $this->parameterNames[] = $name;

            $value = null;

            $hasDefaultValue = $reflectionProperty instanceof ReflectionParameter
                ? $reflectionProperty->isDefaultValueAvailable()
                : $reflectionProperty->hasDefaultValue();

            // check if param was overridden
            if (array_key_exists($name, $overridden)) {
                $value = $overridden[$name];
            } else {
                // if not overridden, try to extract value from input
                // so, if input is an array, we check if param is provided
                if (is_array($input) && array_key_exists($name, $input)) {
                    $value = $input[$name];
                } elseif ($input instanceof ArrayAccess && isset($input[$name])) {
                    $value = $input[$name];
                } elseif (is_object($input) && property_exists($input, $name)) {
                    $value = $input->{$name};
                } else {
                    // if param is not provided via input
                    // check if it has default value
                    if ($hasDefaultValue) {
                        // if default value is provided, then just check next param
                        continue;
                    } else {
                        if (!$type->allowsNull()) {
                            throw new RequiredParameterNotProvidedException($name);
                        }
                    }
                }
            }

            $validated[$name] = $this->validateReflectionProperty($reflectionProperty, $value);
        }

        return $validated;
    }

    /**
     * Says that type name should be provided among parameters in input.
     *
     * @return bool
     */
    public function shouldRequireTypeName(): bool
    {
        return Container::get(Invoke::class)->getConfig("types.alwaysRequireName", false);
    }

    /**
     * Says that type name should be included among parameters in result.
     *
     * @return bool
     */
    public function shouldReturnTypeName(): bool
    {
        return Container::get(Invoke::class)->getConfig("types.alwaysReturnName", false);
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
        $array = [];

        foreach ($this->parameterNames as $parameterName) {
            $value = $this->get($parameterName);

            $array[$parameterName] = Utils::valueToArray($value);
        }

        if ($this->shouldReturnTypeName()) {
            $array["@type"] = Utils::getPipeTypeName($this);
        }

        return $array;
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value, true);
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
}
