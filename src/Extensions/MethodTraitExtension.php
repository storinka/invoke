<?php

namespace Invoke\Extensions;

use Attribute;

/**
 * Says that a trait is an extension to the method thus hooks will be called.
 *
 * Available hooks:
 *   - beforeValidation{traitName}
 *   - beforeHandle{traitName}
 *   - afterHandle{traitName}
 */
#[Attribute]
interface MethodTraitExtension
{
}
