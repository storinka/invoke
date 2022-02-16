<?php

namespace Invoke\Exceptions;

use Invoke\Container;
use Invoke\Invoke;
use Invoke\Utils\Utils;
use RuntimeException;

/**
 * General pipe error.
 */
class PipeException extends RuntimeException
{
    public static function getErrorName(): string
    {
        return Utils::getErrorNameFromException(static::class);
    }

    public function getHttpCode(): int
    {
        $invoke = Container::get(Invoke::class);

        return $invoke->isInputMode() ? 400 : 500;
    }
}
