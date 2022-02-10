<?php

namespace Invoke\Types;

use Invoke\Streams\StreamDecorator;
use Invoke\Type;

/**
 * Binary data type.
 *
 * @see HttpFile
 */
interface BinaryType extends Type, StreamDecorator
{
    public function getType(): string;
}