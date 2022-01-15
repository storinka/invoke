<?php

namespace Invoke;

use Invoke\Exceptions\InvalidFunctionException;

class Invoke
{
    protected static ?Invoke $instance = null;

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
            "resolve" => null,
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
            return call_user_func_array($method, $params);
        }

        $method = new $method;

        return $method($params);
    }

    public static function setMethods(array $methods): void
    {
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
}