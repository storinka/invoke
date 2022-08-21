<?php

namespace Invoke\Support;

use Invoke\Attributes\NotParameter;

/**
 * @method mixed singleRun(mixed $value)
 */
trait WithSingleRun
{
    #[NotParameter]
    private bool $wasRun = false;

    #[NotParameter]
    private mixed $runValue = null;

    public function run(mixed $value): mixed
    {
        if (!$this->wasRun) {
            $this->wasRun = true;
            $this->runValue = $this->singleRun($value);
        }

        return $this->runValue;
    }
}