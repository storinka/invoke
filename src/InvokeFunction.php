<?php

namespace Invoke;

use Closure;
use Invoke\Typesystem\Typesystem;
use Invoke\Typesystem\Undef;

abstract class InvokeFunction
{
    /**
     * Extension traits
     *
     * @var array $registeredTraits
     */
    private array $registeredTraits = [];

    /**
     * Used for documentation.
     *
     * @var string $resultType
     */
    public static string $resultType;

    /**
     * Says that a user must be authenticated before using this function.
     *
     * @var bool $secured
     */
    public static bool $secured = true;

    /**
     * Returns array of params.
     *
     * @return array
     */
    public abstract static function params(): array;

    /**
     * Handle function invocation.
     *
     * @param array $params
     * @return mixed
     */
    protected abstract function handle(array $params);

    /**
     * Prepare function to invocation.
     *
     * @param array $params
     */
    protected function prepare(array $params)
    {
        //
    }

    /**
     * Add access verification.
     *
     * @param array $params
     * @return bool
     */
    protected function guard(array $params): bool
    {
        return true;
    }

    /**
     * Invoke the function.
     *
     * @param array $inputParams
     * @return mixed
     */
    public function invoke(array $inputParams)
    {
        $this->registerTraits();

        $this->executeRegisteredTraits("initialize");

        if (static::$secured) {
            if (!InvokeMachine::isAuthorized()) {
                throw new InvokeError("UNAUTHORIZED", 401);
            }
        }

        $className = static::class;

        $params = static::params();
        $validatedParams = [];
        foreach ($params as $paramName => $paramType) {
            $value = new Undef();

            if (array_key_exists($paramName, $inputParams)) {
                $value = $inputParams[$paramName];
            }

            $value = Typesystem::validateParam($paramName, $paramType, $value);

            if ($value instanceof Undef) {
                continue;
            }

            $validatedParams[$paramName] = $value;
        }

        $this->prepare($validatedParams);
        $this->executeRegisteredTraits("prepare", [$validatedParams]);

        if (!$this->guard($validatedParams)) {
            throw new InvokeError("FORBIDDEN", 403);
        }

        $this->executeRegisteredTraits("guard", [$validatedParams], function ($allowed) {
            if (!$allowed) {
                throw new InvokeError("FORBIDDEN", 403);
            }
        });

        $result = $this->handle($validatedParams);

        if (isset($this::$resultType)) {
            return Typesystem::validateParam("{$className}::{$this::$resultType}", $this::$resultType, $result);
        } else {
            return $result;
        }
    }

    private function registerTraits()
    {
        foreach (class_uses($this) as $trait) {
            $this->registeredTraits[] = $trait;
        }
    }

    private function executeRegisteredTraits(string $name, array $functionParams = [], Closure $handler = null)
    {
        foreach ($this->registeredTraits as $trait) {
            $methodName = $name . invoke_get_class_name($trait);

            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}(...$functionParams);;

                if ($handler) {
                    $handler($result);
                }
            }
        }
    }
}
