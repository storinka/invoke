<?php

namespace Invoke\Typesystem;

use Invoke\Typesystem\Utils\TypeUtils;
use RuntimeException;

/**
 * @method render(): array
 */
abstract class Type implements InvokeType
{
    /**
     * Validated params during hydration.
     *
     * @var array
     */
    private array $_validatedParams = [];

    /**
     * Type params.
     *
     * @return array
     */
    public static function params(): array
    {
        return [];
    }

    /**
     * Validate the data and hydrate the type.
     *
     * @param $data
     */
    protected function hydrate($data): void
    {
        // a bit confusing, prob should be rewritten in the future
        $result = TypeUtils::hydrate($this, $data);

        $this->_validatedParams = $result;
    }

    /**
     * Get validated params..
     *
     * @return array
     */
    public function getValidatedParams(): array
    {
        return $this->_validatedParams;
    }

    // factory methods

    /**
     * Creates an instance of the type. If data is null, then null is returned.
     *
     * @param $data
     * @return static|null
     */
    public static function from($data): ?self
    {
        if (is_null($data)) {
            return null;
        }

        $type = new static();

        $type->hydrate($data);

        return $type;
    }

    /**
     * Create many instances of the type.
     *
     * @param $items
     * @return array
     */
    public static function many($items): array
    {
        if (is_array($items) && invoke_is_assoc($items)) {
            $items = array_values($items);
        }

        return array_map(fn($item) => static::from($item), $items);
    }

    // util methods

    public function jsonSerialize(): array
    {
        return $this->getValidatedParams();
    }

    public function offsetGet($offset)
    {
        return $this->getValidatedParams()[$offset];
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->getValidatedParams());
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException("Unsupported!");
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException("Unsupported!");
    }
}
