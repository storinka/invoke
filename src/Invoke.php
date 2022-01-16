<?php

namespace Invoke;

use Closure;
use Invoke\Exceptions\InvalidFunctionException;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;
use ReflectionFunction;

class Invoke
{
    /**
     * @var Extension[] $extensions
     */
    public static array $extensions = [
    ];

    public static array $methods = [
    ];

    public static array $config = [
        "server" => [
            "pathPrefix" => "invoke"
        ],
        "docs" => [
            "enableMethods" => true,
            "enableUi" => true,
        ],
        "ioc" => [
            "makeFn" => null,
            "callFn" => null,
        ],
        "typesystem" => [
            "strict" => true,
            "typeNames" => true,
        ],
    ];

    public static function invoke(string $name, array $params = []): AsData|int|string|null|bool|array
    {
        $method = static::$methods[$name] ?? null;

        if ($method == null) {
            throw new InvalidFunctionException($name);
        }

        if (is_callable($method) || (is_string($method) && function_exists($method))) {
            $reflectionFunction = new ReflectionFunction($method);

            $params = Typesystem::validateParams(
                ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionFunction->getParameters()),
                $params
            );

            return static::callMethod($method, $params);
        }

        $method = static::makeMethod($method);

        return $method($params);
    }

    public static function callMethod(mixed $method, array $params = [])
    {
        if (!empty($callFn = static::$config["ioc"]["callFn"])) {
            return $callFn($method, $params);
        }

        return call_user_func_array($method, $params);
    }

    public static function makeMethod(string $method,
                                      array  $dependencies = []): Method
    {
        if (!empty($makeFn = static::$config["ioc"]["makeFn"])) {
            return $makeFn($method, $dependencies);
        }

        return (new ReflectionClass($method))->newInstanceArgs($dependencies);
    }

    public static function getMethods(): array
    {
        return static::$methods;
    }

    public static function getMethod(string $name): string|callable|Closure
    {
        return static::$methods[$name];
    }

    public static function setMethods(array $methods): void
    {
        foreach ($methods as $name => $method) {
            if (is_numeric($name) && is_string($method)) {
                unset($methods[$name]);
                $methods[$method] = $method;
            }
        }

        static::$methods = $methods;
    }

    public static function setConfig(array $config): void
    {
        static::$config = array_merge_recursive2(static::$config, $config);
    }

    public static function setup(array $methods = [],
                                 array $config = []): void
    {
        static::setMethods($methods);
        static::setConfig($config);
    }

    public static function serve(): void
    {
        $config = static::$config;

        $url = $_SERVER["REQUEST_URI"];

        // trim spaces and slashes from the url
        $url = trim($url);
        $url = trim($url, "/");

        $urlParts = explode("?", $url);
        if (count($urlParts) > 1) {
            [$path, $queryString] = $urlParts;
        } else {
            [$path] = $urlParts;
            $queryString = "";
        }

        if (!str_starts_with($path, $config["server"]["pathPrefix"])) {
            http_response_code(404);
            return;
        }

        if ($path == $config["server"]["pathPrefix"]) {
            http_response_code(404);
            return;
        }

        $pathParts = explode("/", $path);
        $functionName = end($pathParts);

        $params = [];

        $headers = getallheaders();

        if (array_key_exists("Content-Type", $headers)) {
            $contentType = $headers["Content-Type"];

            if (strpos($contentType, "application/json") > -1) {
                $params = file_get_contents("php://input");

                $params = json_decode($params, true);
            }
        } else {
            parse_str($queryString, $params);
        }

        $result = static::invoke($functionName, $params);

        header("Content-Type: application/json");

        echo json_encode([
            "result" => $result,
        ]);
    }

    public static function registerExtension(Extension $extension)
    {
        if (!in_array($extension, static::$extensions)) {
            static::$extensions[] = $extension;

            $extension->registered();
        }
    }

    public static function unregisterExtension(Extension $extension)
    {
        static::$extensions = array_filter(static::$extensions, function (Extension $e) use ($extension) {
            return $e !== $extension;
        });

        $extension->unregistered();
    }

    public static function callExtensionsHook(string $method, array $params = [])
    {
        foreach (static::$extensions as $extension) {
            $extension->{$method}(...$params);
        }
    }
}