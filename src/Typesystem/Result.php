<?php

namespace Invoke\Typesystem;

use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Exceptions\InvalidParamValueException;
use Invoke\Typesystem\Exceptions\InvalidResultParamTypeException;
use Invoke\Typesystem\Exceptions\InvalidResultParamValueException;

abstract class Result extends AbstractType implements ResultType
{
    public static function create($data)
    {
        if (is_null($data)) {
            return null;
        }

        return new static($data);
    }

    public static function createArray($items): array
    {
        if (invoke_is_assoc($items)) {
            $items = array_values($items);
        }

        return array_map(fn($item) => new static($item), $items);
    }

    protected function validate(): array
    {
        try {
            return parent::validate();
        } catch (InvalidParamTypeException $exception) {
            throw new InvalidResultParamTypeException($exception->getParamName(), $exception->getParamType(), $exception->getActualType());
        } catch (InvalidParamValueException $exception) {
            throw new InvalidResultParamValueException($exception->getParamName(), $exception->getParamType(), $exception->getValue());
        }
    }
}
