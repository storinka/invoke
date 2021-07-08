<?php

namespace Invoke\Typesystem;

abstract class GenericCustomType extends CustomType
{
    /**
     * @var array $genericTypes
     */
    protected array $genericTypes;

    /**
     * @return array
     */
    public function getGenericTypes(): array
    {
        return $this->genericTypes;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $genericTypes = array_map(fn($type) => Typesystem::getTypeName($type), $this->getGenericTypes());
        $genericTypes = implode(", ", $genericTypes);

        $type = Typesystem::getTypeName($this->baseType);

        return "{$type}<{$genericTypes}>";
    }
}
