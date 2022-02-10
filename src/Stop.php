<?php

namespace Invoke;

/**
 * Indicates that pipeline should be stopped.
 */
class Stop implements Pipe
{
    /**
     * @param mixed|null $value value of stop signal
     * @param Pipe|string|null $until stop until another pipe
     */
    public function __construct(public mixed            $value = null,
                                public Pipe|string|null $until = null)
    {
    }

    public function pass(mixed $value): mixed
    {
        return $value;
    }
}