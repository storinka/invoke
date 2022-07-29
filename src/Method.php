<?php

namespace Invoke;

use ArrayAccess;
use Invoke\Attributes\NotParameter;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\PipeException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Exceptions\TypeNameRequiredException;
use Invoke\Support\AsInvokeParameterValues;
use Invoke\Support\TypeWithParams;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;

/**
 * Abstract method pipe.
 *
 * @template R
 */
abstract class Method extends TypeWithParams
{
    #[NotParameter]
    private array $handleParameters = [];

    /**
     * @inheritDoc
     */
    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        // call "beforeValidation" hook on extensions
        Invoke::callMethodExtensionsHook($this, "beforeValidation");

        // get current instance of invoke from container
        $invoke = Container::get(Invoke::class);

        // enable input mode
        $invoke->setInputMode(true);

        $usePropertiesAsParameters = $invoke->getConfig("methods.usePropertiesAsParameters", true);

        if ($usePropertiesAsParameters) {
            // validate parameters
            parent::pass($input);
        }

        $this->handleParameters = $this->validateHandleMethod($input);

        // disable input mode
        $invoke->setInputMode(false);

        // call "beforeHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "beforeHandle");

        // handle the method
        $result = $this->handle(...$this->handleParameters);

        // call "afterHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "afterHandle", [$result]);

        return $result;
    }

    private function validateHandleMethod(array $input): array
    {
        $reflectionClass = ReflectionUtils::getClass($this::class);
        $handleMethod = $reflectionClass->getMethod("handle");

        if (!$handleMethod->isProtected()) {
            throw new \RuntimeException("\"handle\" method of {$reflectionClass->name} must be protected.");
        }

        return $this->validate($handleMethod->getParameters(), $input, true);
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
     * @inheritDoc
     */
    public function invoke_getUsedTypes(): array
    {
        $pipes = parent::invoke_getUsedTypes();

        $reflectionClass = ReflectionUtils::getClass($this::class);
        $reflectionMethod = $reflectionClass->getMethod("handle");

        return [...$pipes, ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod)];
    }

    /**
     * @inheritDoc
     */
    public function shouldRequireTypeName(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldReturnTypeName(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public static function invoke_getTypeName(): string
    {
        return Utils::getMethodNameFromClass(static::class);
    }

    /**
     * Invoke the method.
     *
     * @param array $params
     * @return R
     */
    public static function invoke(array $params = []): mixed
    {
        $method = Container::make(static::class);

        return Piping::run($method, $params);
    }

    public function get(string $name): mixed
    {
        if (isset($this->handleParameters[$name])) {
            return $this->handleParameters[$name];
        }

        return parent::get($name);
    }
}
