<?php

namespace Invoke;

/**
 * Basic component of Invoke.
 */
interface Pipe
{
    public function pass(mixed $value): mixed;

    public function getTypeName(): string;

    public function getValueTypeName(mixed $value): string;

    public function getUsedPipes(): array;
}
