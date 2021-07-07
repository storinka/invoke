<?php

namespace Invoke\V1\Typesystem;

abstract class GenericCustomTypeV1 extends CustomTypeV1
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
        $genericTypes = array_map(fn($type) => TypesystemV1::getTypeName($type), $this->getGenericTypes());
        $genericTypes = implode(", ", $genericTypes);

        $type = TypesystemV1::getTypeName($this->baseType);

        return "{$type}<{$genericTypes}>";
    }
}
