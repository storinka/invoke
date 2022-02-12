<?php

namespace Invoke\Types;

use Invoke\Streams\StreamDecorator;
use Invoke\Toolkit\Types\InputFile;
use Invoke\Type;

/**
 * Binary data type.
 *
 * @see InputFile
 */
interface BinaryType extends Type, StreamDecorator
{
}