<?php

namespace Invoke\Support;

use Invoke\Attributes\NotParameter;
use RuntimeException;

/**
 * @method mixed singleRun(mixed $value)
 */
trait WithSingleRunOrThrow
{
    #[NotParameter]
    private bool $wasRun = false;

    public function run(mixed $value): mixed
    {
        if ($this->wasRun) {
            throw new RuntimeException('Cannot run again through "' . static::class . '"');
        }

        $this->wasRun = true;

        return $this->singleRun($value);
    }
}