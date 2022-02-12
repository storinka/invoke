<?php

namespace Invoke;

use Throwable;

/**
 * Connect multiple pipes into a pipeline.
 */
class Pipeline implements Pipe
{
    protected array $pipes = [];
    protected array $catchPipes = [];

    public function pass(mixed $value): mixed
    {
        try {
            foreach ($this->pipes as $pipe) {
                $value = Piping::run($pipe, $value);
            }
        } catch (Throwable $exception) {
            $value = Piping::run($this->catchPipes, $exception);
        }

        return $value;
    }
}