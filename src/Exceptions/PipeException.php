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
    private int $httpCode;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $invoke = Container::get(Invoke::class);
        $this->httpCode = $invoke->isInputMode() ? 400 : 500;
    }

    public static function getErrorName(): string
    {
        return Utils::getErrorNameFromException(static::class);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}
