<?php

namespace Invoke\Meta;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
interface Inject
{
}
