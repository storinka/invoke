<?php

namespace InvokeTests\Types;

use Invoke\Support\Singleton;
use Invoke\Type;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

abstract class TypeBasedTestCase extends TestCase
{
    protected bool $singleton = true;

    abstract protected function getType(): Type;

    abstract protected function getTypeName(): string;

    public function testBasics(): void
    {
        $type = $this->getType();

        if ($this->singleton) {
            assertInstanceOf(Singleton::class, $type);
        }

        assertEquals($type::invoke_getTypeName(), $this->getTypeName());
    }
}