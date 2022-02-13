<?php

namespace Invoke\Documentation\Documents;

/**
 * Invoke instruction document.
 */
class InvokeInstructionDocument extends Document
{
    /**
     * Unique instruction name.
     *
     * @var string
     */
    public string $name; // default

    /**
     * Protocol.
     *
     * @var string $protocol
     */
    public string $protocol; // http/https

    /**
     * Host.
     *
     * @var string $host
     */
    public string $host; // localhost

    /**
     * Port.
     *
     * @var int $port
     */
    public int $port; // port

    /**
     * Path.
     *
     * @var string|null $path
     */
    public ?string $path; // invoke

    /**
     * Data type.
     *
     * @var string $type
     */
    public string $type; // json
}