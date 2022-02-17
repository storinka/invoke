<?php

namespace Invoke\Support;

use Invoke\Data;
use Invoke\Method;

/**
 * Allow using readonly properties inside {@see Data} and {@see Method}.
 *
 * @mixin TypeWithParams
 */
trait WithReadonlyParams
{
    public function set($name, $value)
    {
        parent::set($name, $value);
    }
}
