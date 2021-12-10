<?php

namespace Invoke;

use Invoke\Typesystem\Typesystem;
use Invoke\Typesystem\Utils\ReflectionUtils;
use RuntimeException;

class InvokeMachine
{
    /**
     * @var array $functionsFullTree
     */
    protected static array $functionsFullTree = [];

    /**
     * @var array $configuration
     */
    protected static array $configuration = [
        "strict" => false,
    ];

    /**
     * @param array $functionsTree
     * @param array $configuration
     */
    public static function setup(array $functionsTree, array $configuration = [])
    {
        static::$functionsFullTree = [];

        $normalizedFunctionsTree = [];
        foreach ($functionsTree as $functionsVersion => $functionsList) {
            $normalizedFunctionsTree[Versions::semver($functionsVersion)] = $functionsList;
        }

        // sort functions tree using semver
        uksort($normalizedFunctionsTree, "version_compare");

        foreach ($normalizedFunctionsTree as $functionsVersion => $functionsList) {
            // get previous version functions list
            $prevVersionFunctionsList = end(static::$functionsFullTree);
            if (!$prevVersionFunctionsList) {
                $prevVersionFunctionsList = [];
            }

            // create new version functions list
            static::$functionsFullTree[$functionsVersion] = [];

            // copy previous version functions list to new
            foreach ($prevVersionFunctionsList as $prevVersionFunctionName => $prevVersionFunctionClass) {
                static::$functionsFullTree[$functionsVersion][$prevVersionFunctionName] = $prevVersionFunctionClass;
            }

            // set new functions list
            foreach ($functionsList as $functionName => $functionClass) {
                // check if function is not null
                // if null, then we remove this function
                if ($functionClass) {
                    static::$functionsFullTree[$functionsVersion][$functionName] = $functionClass;
                } else {
                    unset(static::$functionsFullTree[$functionsVersion][$functionName]);
                }
            }
        }

        static::$configuration = array_merge(static::$configuration, $configuration);
    }

    public static function functionsFullTree(): array
    {
        return static::$functionsFullTree;
    }

    public static function currentVersionFunctionsTree(): array
    {
        return static::$functionsFullTree[static::version()] ?? [];
    }

    public static function configuration(?string $name = null, $default = null)
    {
        if (isset($name)) {
            return static::$configuration[$name] ?? $default;
        }

        return static::$configuration;
    }

    public static function invoke(string $functionName, array $inputParams, $version = null)
    {
        $functionOrClass = static::getFunctionClass($functionName, $version);

        return static::invokeFunction(
            function_exists($functionOrClass) ? $functionOrClass : new $functionOrClass,
            $inputParams
        );
    }

    public static function invokeFunction($function, $inputParams)
    {
        if ($function instanceof InvokeFunction) {
            return $function($inputParams);
        }

        if (function_exists($function)) {
            return InvokeMachine::invokeNativeFunction($function, $inputParams);
        }

        throw new RuntimeException("Invalid function: {$function}");
    }

    public static function invokeNativeFunction($function, array $inputParams)
    {
        $reflectionFunction = new \ReflectionFunction($function);
        $reflectionParameters = $reflectionFunction->getParameters();

        $params = ReflectionUtils::inspectFunctionReflectionParameters($reflectionParameters);

        $validatedParams = [];

        foreach ($params as $paramName => $paramType) {
            $value = $inputParams[$paramName] ?? null;

            $value = Typesystem::validateParam($paramName, $paramType, $value);

            $validatedParams[$paramName] = $value;
        }

        $neededParams = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            $refParamName = $reflectionParameter->getName();

            if ($refParamName === "params" && !array_key_exists("params", $validatedParams)) {
                array_push($neededParams, $validatedParams);
            } else {
                array_push($neededParams, $validatedParams[$refParamName]);
            }
        }

        return $function(...$neededParams);
    }

    public static function getFunctionClass(string $functionName, $version = null)
    {
        if (!$version) {
            $version = static::version();
        } else {
            $version = static::getClosestVersion($version);
        }

        if (!isset(static::$functionsFullTree[$version])) {
            throw new InvalidVersionException($version);
        }

        if (!isset(static::$functionsFullTree[$version][$functionName])) {
            throw new InvalidFunctionException($functionName);
        }

        return static::$functionsFullTree[$version][$functionName];
    }

    public static function version(): string
    {
        $versions = array_keys(static::functionsFullTree());
        return end($versions);
    }

    public static function getClosestVersion($version)
    {
        if (!empty(static::$functionsFullTree[$version])) {
            return $version;
        }

        $semver = Versions::semver($version);
        if (!empty(static::$functionsFullTree[$semver])) {
            return $semver;
        }

        [$major, $minor, $patch] = Versions::parseSemver($version);

        $minorI = $minor;
        if ($minorI === null) {
            $minorI = PHP_INT_MAX;
        }
        $patchI = $patch;
        if ($patchI === null) {
            $patchI = PHP_INT_MAX;
        }

        $closestVersion = invoke_closest_semver(
            "$major.$minorI.$patchI",
            array_keys(static::$functionsFullTree)
        );

        if ($closestVersion === null) {
            throw new InvalidVersionException($version);
        }

        [$closestMajor, $closestMinor, $closestPatch] = Versions::parseSemver($closestVersion);

        if ($major !== $closestMajor) {
            throw new InvalidVersionException($version);
        }

        if ($minor !== null) {
            if ($minor !== $closestMinor) {
                throw new InvalidVersionException($version);
            }

            if ($patch !== null) {
                if ($patch !== $closestPatch) {
                    throw new InvalidVersionException($version);
                }
            }
        }

        return $closestVersion;
    }

    public static function handleRequest(?string $uri = null, ?array $params = null)
    {
        if (is_null($uri)) {
            $uri = $_SERVER["REQUEST_URI"];
        }

        $uri = trim($uri);
        $uri = trim($uri, "/");

        [$path, $queryString] = explode("?", $uri);
        [$prefix, $version, $functionName] = explode("/", $path);

        if (is_null($params)) {
            $params = [];

            $headers = getallheaders();

            if (array_key_exists("Content-Type", $headers)) {
                $contentType = $headers["Content-Type"];

                if (strpos($contentType, "application/json") > -1) {
                    $body = file_get_contents("php://input");

                    $params = json_decode($body, true);
                }
            } else {
                parse_str($queryString, $params);
            }
        }

        $result = InvokeMachine::invoke($functionName, $params, $version);

        header("Content-Type: application/json");

        echo json_encode([
            "result" => $result,
        ]);

        die();
    }
}
