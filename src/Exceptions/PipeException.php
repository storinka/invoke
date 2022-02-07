<?php

namespace Invoke\Exceptions;

use Invoke\Invoke;
use Invoke\Utils\Utils;
use RuntimeException;

class PipeException extends RuntimeException
{
    public static function getErrorName(): string
    {
        return Utils::getErrorNameFromException(static::class);
    }

    public function getHttpCode(): int
    {
        return Invoke::isInputMode() ? 400 : 500;
    }
}