<?php

namespace Invoke;

use Invoke\Exceptions\PipeException;
use Invoke\Pipes\FunctionPipe;

class Invoke extends AbstractSingletonPipe
{
    protected static Invoke $instance;

    protected array $methods = [];
    protected array $config = [
        "server" => [
            "pathPrefix" => "invoke"
        ],
        "inputMode" => [
            "convertStrings" => true,
        ],
    ];
    public bool $inputMode = false;

    public function pass(mixed $value): mixed
    {
        $name = $value["name"];
        $params = $value["params"];

        return static::invoke(
            $name,
            $params
        );
    }

    public static function config(string $property): mixed
    {
        $path = explode(".", $property);

        $value = static::getInstance()->config;

        foreach ($path as $key) {
            $value = $value[$key];
        }

        return $value;
    }

    public static function invoke(string $name, array $params = [])
    {
        $method = static::getInstance()->methods[$name];

        if (is_callable($method)) {
            $method = new FunctionPipe($method);
        }

        return Pipeline::make($method, $params);
    }

    public static function setup(array $methods = [])
    {
        foreach ($methods as $name => $method) {
            if (is_numeric($name) && is_string($method)) {
                unset($methods[$name]);

                if (class_exists($method)) {
                    $methods[Utils::getMethodNameFromClass($method)] = $method;
                } else {
                    $methods[$method] = $method;
                }
            }
        }

        static::getInstance()->methods = $methods;
    }

    public static function serve($modeOrPipe = HttpPipe::class)
    {
        try {
            $result = Pipeline::make($modeOrPipe, static::getInstance());

            echo json_encode([
                "result" => $result,
            ]);
        } catch (PipeException $exception) {
            http_response_code($exception->getHttpCode());

            echo json_encode([
                "code" => $exception->getHttpCode(),
                "error" => $exception::getErrorName(),
                "message" => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            http_response_code(500);

            echo json_encode([
                "code" => $exception->getCode(),
                "error" => "SERVER_ERROR",
                "message" => $exception->getMessage(),
                "trace" => $exception->getTrace(),
            ]);
        }
    }

    public static function isInputMode(): bool
    {
        return static::getInstance()->inputMode;
    }

    public static function setInputMode(bool $inputMode): void
    {
        static::getInstance()->inputMode = $inputMode;
    }

    public static function getMethods(): array
    {
        return static::getInstance()->methods;
    }
}