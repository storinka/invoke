<?php

namespace Invoke\Pipelines;

use Invoke\Pipe;
use Invoke\Pipelines\Http\HttpPipeline;
use Invoke\Piping;

class MainPipeline implements Pipe
{
    public function pass(mixed $value): mixed
    {
        return Piping::run(HttpPipeline::class, $value);
    }
}
