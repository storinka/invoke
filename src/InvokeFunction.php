<?php

namespace Invoke;

use Invoke\Typesystem\Typesystem;
use Invoke\Typesystem\Undef;
use RuntimeException;

abstract class InvokeFunction
{
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
        if (static::$secured) {
            if (!InvokeMachine::isAuthorized()) {
                throw new RuntimeException("Unauthorized", 401);
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

        if (method_exists($this, "beforeInvoke")) {
            $this->beforeInvoke($validatedParams);
        }

        if (!$this->guard($validatedParams)) {
            throw new RuntimeException("Forbidden", 403);
        }

        $result = $this->handle($validatedParams);

        if (isset($this::$resultType)) {
            return Typesystem::validateParam("{$className}::{$this::$resultType}", $this::$resultType, $result);
        } else {
            return $result;
        }
    }
}
