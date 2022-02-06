<?php

namespace Invoke\Validations;

use Attribute;
use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Pipes\ClassPipe;
use Invoke\Pipes\UnionPipe;
use Invoke\Utils\ReflectionUtils;
use Invoke\Validation;

#[Attribute]
class ArrayOf extends Validation
{
    public Pipe $itemPipe;

    public function __construct(mixed $itemPipe)
    {
        if (is_array($itemPipe)) {
            $this->itemPipe = new UnionPipe($itemPipe);
        } else if (is_string($itemPipe)) {
            if (class_exists($itemPipe)) {
                $this->itemPipe = new ClassPipe($itemPipe);
            } else {
                $this->itemPipe = ReflectionUtils::typeToPipe($itemPipe);
            }
        } else {
            $this->itemPipe = $itemPipe;
        }
    }

    public function pass(mixed $value): mixed
    {
        foreach ($value as $index => $item) {
            $value[$index] = Pipeline::catcher(
                fn() => $this->itemPipe->pass($item),
                "{$this->parentPipe->getTypeName()}::{$this->paramName}[$index]"
            );
        }

        return $value;
    }

    public function getValidationData(): array
    {
        return [
            "itemType" => $this->itemPipe,
        ];
    }
}