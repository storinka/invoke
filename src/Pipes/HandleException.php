<?php

namespace Invoke\Pipes;

use Invoke\Pipe;
use Invoke\Stop;
use Invoke\Types\FailedResponseData;
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

        return FailedResponseData::from([
            "code" => $value->getCode(),
            "error" => $value::class,
            "message" => $value->getMessage(),
        ]);
    }
}
