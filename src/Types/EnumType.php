<?php

namespace Invoke\Types;

use BackedEnum;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Pipeline;
use Invoke\Stop;
use Invoke\Support\HasDynamicName;
use Invoke\Type;

class EnumType implements Type, HasDynamicName
{
    /** @var class-string<BackedEnum> $typeClass */
    public readonly string $enumClass;

    /** @var class-string<BackedEnum> $typeClass */
    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }
        
        if (is_object($value)) {
            if ($value::class === $this->enumClass) {
                return $value;
            }
        }

        if (is_subclass_of($this->enumClass, BackedEnum::class)) {
            $value = Pipeline::pass(new UnionType([StringType::class, IntType::class]), $value);

            $enum = $this->enumClass::tryFrom($value);

            if ($enum !== null) {
                return $enum;
            }
        }

        throw new InvalidTypeException($this, $value);
    }

    public static function getName(): string
    {
        return "enum";
    }

    public function getDynamicName(): string
    {
        $className = invoke_get_class_name($this->enumClass);

        if (!is_subclass_of($this->enumClass, BackedEnum::class)) {
            return "enum<$className>";
        }

        $values = array_map(
            fn(BackedEnum $enum) => $enum->value,
            $this->enumClass::cases()
        );

        $values = implode("|", $values);

        return "enum<$className>({$values})";
    }
}