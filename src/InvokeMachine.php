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
     * @param array $functionsTree
     * @param array $configuration
     */
    public static function setup(array $functionsTree, array $configuration = [])
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
    }

    public static function functionsTree(): array
    {
        return static::$functionsTree;
    }

    public static function functionsFullTree(): array
    {
        return static::$functionsFullTree;
    }

    public static function currentVersionFunctionsTree(): array
    {
        return static::$functionsFullTree[static::version()] ?? [];
    }

    public static function configuration(?string $name = null)
    {
        if (isset($name)) {
            return static::$configuration[$name];
        }

        return static::$configuration;
    }

    public static function invoke(string $functionName, array $inputParams, int $version = null)
    {
        if (!$version) {
            $version = static::version();
        }

        if (!isset(static::$functionsFullTree[$version])) {
            throw new InvokeError("INVALID_VERSION", 400);
        }

        if (!isset(static::$functionsFullTree[$version][$functionName])) {
            throw new InvalidFunctionException($functionName);
        }

        $functionClass = static::$functionsFullTree[$version][$functionName];
        $functionInstance = new $functionClass;

        return $functionInstance->invoke($inputParams);
    }

    public static function version(): int
    {
        $versions = array_keys(static::functionsFullTree());
        return end($versions);
    }
}
