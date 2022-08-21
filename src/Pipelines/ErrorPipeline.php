<?php

namespace Invoke\Pipelines;

use Invoke\Pipe;
use Invoke\Piping;
use RuntimeException;
use Throwable;

class ErrorPipeline implements Pipe
{
    public function run(mixed $value): mixed
    {
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
