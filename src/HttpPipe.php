<?php

namespace Invoke;

use Invoke\Exceptions\PipeException;

/**
 * HTTP request handler pipe.
 */
class HttpPipe implements Pipe
{
    public function pass(mixed $value): mixed
    {
        try {
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

            if (!str_starts_with($path, Invoke::config("server.pathPrefix"))) {
                http_response_code(404);
                return null;
            }

            if ($path == Invoke::config("server.pathPrefix")) {
                http_response_code(404);
                return null;
            }

            $pathParts = explode("/", $path);
            $method = end($pathParts);

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

            header("Content-Type: application/json");

            $result = Invoke::invoke($method, $params);

            return json_encode([
                "result" => $result,
            ]);
        } catch (PipeException $exception) {
            http_response_code($exception->getHttpCode());

            return json_encode([
                "code" => $exception->getHttpCode(),
                "error" => $exception::getErrorName(),
                "message" => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            http_response_code(500);

            return json_encode([
                "code" => $exception->getCode(),
                "error" => "SERVER_ERROR",
                "message" => $exception->getMessage(),
                "trace" => $exception->getTrace(),
            ]);
        }
    }
}