<?php

namespace Invoke\Validators;

use Attribute;
use Invoke\Pipe;
use Invoke\Pipes\ArrayPipe;
use Invoke\Utils;
use Invoke\Validator;

#[Attribute]
class ArrayOf extends Validator
{
    public Pipe $itemPipe;

    public function __construct(mixed $itemPipe)
    {
        $this->itemPipe = Utils::toPipe($itemPipe);
    }

    public function pass(mixed $value): mixed
    {
        foreach ($value as $index => $item) {
            $value[$index] = $this->itemPipe->pass($item);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        $arrayTypeName = ArrayPipe::getInstance()->getTypeName();
        $itemPipeName = $this->itemPipe->getTypeName();

        return "{$arrayTypeName}<{$itemPipeName}>";
    }

    public function getUsedPipes(): array
    {
        return [$this->itemPipe];
    }

    public function toType(): Pipe
    {
        return ArrayPipe::getInstance();
    }
}