<?php

namespace Invoke\Typesystem;

class CustomType
{
    public $type;
    public $handle;
    public $string;

    /**
     * @param string|array $type
     * @param callable $handle
     * @param string|null $string
     */
    public function __construct($type, callable $handle, $string = null)
    {
        $this->type = $type;
        $this->handle = $handle;
        $this->string = $string;
    }

    /**
     * @param $paramName
     * @param $value
     * @return mixed
     */
    public final function validate($paramName, $value)
    {
        if (isset($this->type) && !is_null($this->type)) {
            $value = Typesystem::validateParam($paramName, $this->type, $value);
        }

        return call_user_func($this->handle, $paramName, $value);
    }
}
