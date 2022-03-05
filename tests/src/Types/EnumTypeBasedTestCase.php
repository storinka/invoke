<?php

namespace InvokeTests\Types;

abstract class EnumTypeBasedTestCase extends TypeTestCase
{
    protected bool $singleton = false;

    protected function getTypeName(): string
    {
        return "enum";
    }
}