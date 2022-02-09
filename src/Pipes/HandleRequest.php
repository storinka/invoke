<?php

namespace Invoke\Pipes;

use Invoke\Container\Container;
use Invoke\Exceptions\PipeException;
use Invoke\Invoke;
use Invoke\Pipe;
use Invoke\Stop;
use Invoke\Types\BinaryType;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

/**
 * HTTP request handler pipe.
 */
class HandleRequest implements Pipe
{
    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function pass(mixed $request): mixed
    {
        if ($request instanceof Stop) {
            return $request;
        }

        try {
            if (!($request instanceof ServerRequestInterface)) {
                throw new RuntimeException("The value for HandleRequest pipe must be a ServerRequestInterface.");
            }

            $response = new Response();

            $container = Container::getInstance();

            $container->singleton(RequestInterface::class, $request);
            $container->singleton(ServerRequestInterface::class, $request);
            $container->singleton(ResponseInterface::class, $response);

            $pathPrefix = Invoke::config("server.pathPrefix");
            $pathPrefix = trim($pathPrefix, "/");

            $path = $request->getUri()->getPath();
            $path = trim($path, "/");

            if (!str_starts_with($path, $pathPrefix)) {
                return $response->withStatus(404);
            }

            if ($path == $pathPrefix) {
                return $response->withStatus(404);
            }

            $pathParts = explode("/", $path);

            $method = end($pathParts);

            if (!Invoke::hasMethod($method)) {
                return $response->withStatus(404);
            }

            $params = array_merge(
                $request->getUploadedFiles(),
                $request->getParsedBody() ?? [],
                $request->getQueryParams(),
            );

            $response = $response->withHeader("Content-Type", "application/json");

            $result = Invoke::invoke($method, $params);

            if ($result instanceof ResponseInterface) {
                return $result;
            }

            if ($result instanceof BinaryType) {
                return $response
                    ->withHeader("Content-Type", $result->getType())
                    ->withBody($result->getStream());
            }

            $response
                ->getBody()
                ->write(json_encode([
                    "result" => $result
                ]));

            return $response;
        } catch (PipeException $exception) {
            return new Response(
                $exception->getHttpCode(),
                ["Content-Type", "application/json"],
                json_encode([
                    "code" => $exception->getHttpCode(),
                    "error" => $exception::getErrorName(),
                    "message" => $exception->getMessage(),
                ])
            );
        } catch (Throwable $exception) {
            return new Response(
                500,
                ["Content-Type", "application/json"],
                json_encode([
                    "code" => $exception->getCode(),
                    "error" => "SERVER_ERROR",
                    "message" => $exception->getMessage(),
                    "trace" => $exception->getTrace(),
                ])
            );
        }
    }
}