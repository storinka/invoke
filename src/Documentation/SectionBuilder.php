<?php

namespace Invoke\Documentation;

use Invoke\Pipe;
use Invoke\Documentation\Documents\SectionDocument;

abstract class SectionBuilder implements Pipe
{
    public abstract function build(array $types, array $methods): SectionDocument;

    public function pass(mixed $value): mixed
    {
        return $this->build(
            $value["types"],
            $value["methods"]
        );
    }
}