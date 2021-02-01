<?php

namespace Invoke;

class InvokeMachine
{
    /**
     * @var array $functionsTree
     */
    protected static array $functionsTree = [];

    /**
     * @var array $functionsFullTree
     */
    protected static array $functionsFullTree = [];

    /**
     * @var array $configuration
     */
    protected static array $configuration = [
        "strict" => true,
    ];

    /**
     * @var InvokeUserAuthorization|mixed $authorization
     */
    protected static $authorization;

    /**
     * @param array $functionsTree
     * @param array $configuration
     * @param InvokeUserAuthorization|mixed $authorization
     */
    public static function setup(array $functionsTree, array $configuration = [], $authorization = null)
    {
        ksort($functionsTree);

        static::$functionsTree = $functionsTree;

        foreach ($functionsTree as $functionsVersion => $functionsList) {
            static::$functionsFullTree[$functionsVersion] = [];

            // clone previous version
            $prevVersionFunctionsList = static::$functionsFullTree[$functionsVersion - 1] ?? [];
            foreach ($prevVersionFunctionsList as $prevVersionFunctionName => $prevVersionFunctionClass) {
                static::$functionsFullTree[$functionsVersion][$prevVersionFunctionName] = $prevVersionFunctionClass;
            }

            // fill out new functions
            foreach ($functionsList as $functionName => $functionClass) {
                static::$functionsFullTree[$functionsVersion][$functionName] = $functionClass;
            }
        }

        static::$configuration = array_merge(static::$configuration, $configuration);
        static::$authorization = $authorization;
    }

    public static function functionsTree(): array
    {
        return static::$functionsTree;
    }

    public static function functionsFullTree(): array
    {
        return static::$functionsFullTree;
    }

    public static function configuration(?string $name = null)
    {
        if (isset($name)) {
            return static::$configuration[$name];
        }

        return static::$configuration;
    }

    public static function isAuthorized(): bool
    {
        return isset(static::$authorization);
    }

    public static function authorization()
    {
        return static::$authorization;
    }

    public static function invoke(string $functionName, array $inputParams, int $version)
    {
        $functionClass = static::$functionsFullTree[$version][$functionName] ?? null;

        if (is_null($functionClass)) {
            throw new InvalidFunctionException($functionName);
        }

        $function = new $functionClass;

        return $function->invoke($inputParams);
    }

    public static function version(): int
    {
        $versions = array_keys(static::functionsFullTree());
        return end($versions);
    }
}
