<?php

declare(strict_types=1);

namespace Invoke\Types;

use BackedEnum;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Pipeline;
use Invoke\Stop;
use Invoke\Support\HasDynamicName;
use Invoke\Type;
use Invoke\Utils\Utils;
use UnitEnum;

use function assert;
use function class_implements;
use function Invoke\Utils\get_class_name;
use function is_subclass_of;
use function var_dump;

class EnumType implements Type, HasDynamicName
{
    /** @var class-string<UnitEnum> $typeClass */
    public readonly string $enumClass;

    /** @var class-string<UnitEnum> $typeClass */
    public function __construct(string $enumClass)
    {
        assert(is_subclass_of($enumClass, UnitEnum::class));

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
            $reflectionEnum = new \ReflectionEnum($this->enumClass);
            $value = Utils::typeNameToPipe((string)$reflectionEnum->getBackingType())->pass($value);


            $enum = $this->enumClass::tryFrom($value);

            if ($enum !== null) {
                return $enum;
            }
        }

        throw new InvalidTypeException($this, $value);
    }

    public static function invoke_getName(): string
    {
        return "enum";
    }

    public function invoke_getDynamicName(): string
    {
        return get_class_name($this->enumClass);
    }
}
