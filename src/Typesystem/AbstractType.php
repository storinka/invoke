<?php

namespace Invoke\Typesystem;

use JsonSerializable;

abstract class AbstractType implements JsonSerializable
{
    /**
     * @var mixed
     */
    protected $input = null;

    /**
     * @var array
     */
    protected array $validatedAttributes = [];

    /**
     * @param mixed $input
     */
    public function __construct($input)
    {
        $this->input = $input;

        $this->validate();
    }

    /**
     * Validate data and cache validated.
     *
     * @return array
     */
    protected function validate(): array
    {
        $input = $this->input;

        $params = static::params();
        $renderedParams = method_exists($this, "render") ? $this->render($this->input) : [];

        $result = [];

        foreach ($params as $paramName => $paramType) {
            $value = new Undef();

            if (array_key_exists($paramName, $renderedParams)) {
                $value = $renderedParams[$paramName];
            } else if (is_object($input)) {
                if (method_exists($input, "getRenderAttributes")) {
                    $input = $input->getRenderAttributes();

                    if (array_key_exists($paramName, $input)) {
                        $value = $input[$paramName];
                    }
                } else if (property_exists($input, $paramName)) {
                    $value = $input->{$paramName};
                }
            } else if (
                is_array($input) &&
                array_key_exists($paramName, $input)
            ) {
                $value = $input[$paramName];
            }

            $value = Typesystem::validateParam($paramName, $paramType, $value);

            if ($value instanceof Undef) {
                continue;
            }

            $result[$paramName] = $value;
        }

        $this->validatedAttributes = $result;

        return $result;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return array
     */
    public function getValidatedAttributes(): array
    {
        return $this->validatedAttributes;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->validatedAttributes[$name];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->getValidatedAttributes();
    }

    /**
     * Get validated property if exist.
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->validatedAttributes)) {
            return $this->validatedAttributes[$name];
        }

        return $this->{$name};
    }

    /**
     * @return array
     */
    public static abstract function params(): array;
}
