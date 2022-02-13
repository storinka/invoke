<?php

namespace Invoke\Pipelines\Http\Pipes;

use Invoke\Container;
use Invoke\Exceptions\PipeException;
use Invoke\Pipe;
use Invoke\Pipelines\Http\Data\FailedResponseData;
use Invoke\Stop;
use Invoke\Utils\Utils;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class HandleException implements Pipe
{
    /**
     * @param Throwable $value
     * @return mixed
     */
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        if (!($value instanceof Throwable)) {
            throw new RuntimeException("The value for HandleException pipe must be a Throwable.");
        }

        $response = Container::get(ResponseInterface::class);
        if ($value instanceof PipeException) {
            $response = $response->withStatus($value->getHttpCode());
        } else {
            $response = $response->withStatus(500);
        }
        Container::singleton(ResponseInterface::class, $response);

        return FailedResponseData::from([
            "code" => $response->getStatusCode(),
            "error" => Utils::getErrorNameFromException($value::class),
            "message" => $value->getMessage(),
        ]);
    }
}
