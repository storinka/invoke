<?php

namespace Invoke\Attributes;

use Attribute;
use Invoke\Data;
use Invoke\Method;

/**
 * Says that a property/parameter must not be identified as a parameter of {@see Data} or {@see Method}.
 */
#[Attribute]
class NotParameter
{
}
