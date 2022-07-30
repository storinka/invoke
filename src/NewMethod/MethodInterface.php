<?php

namespace Invoke\NewMethod;

use Invoke\NewMethod\Description\HasMethodDescription;
use Invoke\NewMethod\Information\HasParametersInformation;
use Invoke\Pipe;

/**
 * Abstract method interface.
 *
 * A method must contain:
 * - parameters information
 * - result type information
 * - description (short/full)
 */
interface MethodInterface extends Pipe, HasParametersInformation, HasMethodDescription
{
}