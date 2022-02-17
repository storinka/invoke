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
    /**
     * @param string $name
     * @param mixed $value
     * @param bool $validate
     * @return void
     */
    public function set(string $name, mixed $value, bool $validate = true): void
    {
        parent::set($name, $value);
    }
}
