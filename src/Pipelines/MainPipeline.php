<?php

namespace Invoke\Pipelines;

use Invoke\Pipe;
use Invoke\Piping;
use RuntimeException;

class MainPipeline implements Pipe
{
    public function run(mixed $value): mixed
    {
        if (class_exists("Invoke\Http\HttpPipeline")) {
            return Piping::run("Invoke\Http\HttpPipeline", $value);
        } else {
            throw new RuntimeException("No pipeline found to run.");
        }
    }
}
