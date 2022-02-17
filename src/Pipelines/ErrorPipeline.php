<?php

namespace Invoke\Pipelines;

use Invoke\Pipe;
use Invoke\Piping;
use Invoke\Stop;
use RuntimeException;
use Throwable;

class ErrorPipeline implements Pipe
{
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        if (!($value instanceof Throwable)) {
            throw new RuntimeException("The value for ExceptionPipeline pipe must be a Throwable.");
        }

        if (class_exists("Invoke\Http\HttpErrorPipeline")) {
            return Piping::run("Invoke\Http\HttpErrorPipeline", $value);
        } else {
            throw $value;
        }
    }
}
