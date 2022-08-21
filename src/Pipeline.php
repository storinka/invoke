<?php

namespace Invoke;

/**
 * Connect multiple pipes into a pipeline.
 */
class Pipeline implements Pipe
{
    protected array $pipes = [];

    public function run(mixed $value): mixed
    {
        foreach ($this->pipes as $pipe) {
            $value = Piping::run($pipe, $value);
        }

        return $value;
    }
}