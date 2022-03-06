<?php

namespace InvokeTests\Method;

use Invoke\Exceptions\RequiredParameterNotProvidedException;
use InvokeTests\Method\Fixtures\SomeMethod;
use InvokeTests\Method\Fixtures\SomeMethodWithPublicHandle;
use InvokeTests\TestCase;

class ExceptionsTest extends TestCase
{
    protected function invalidBasicsProvider(): array
    {
        return [
            [[], RequiredParameterNotProvidedException::class, "paramAsProperty"],
            [['paramAsProperty' => 123], RequiredParameterNotProvidedException::class, "paramAsArg"],
            [['paramAsArg' => 123], RequiredParameterNotProvidedException::class, "paramAsProperty"]
        ];
    }

    /**
     * @dataProvider invalidBasicsProvider
     */
    public function testInvalidBasics($input, $exceptionClass, ...$exceptionParams)
    {
        $this->expectExceptionObject(new $exceptionClass(...$exceptionParams));

        SomeMethod::invoke($input);
    }

    public function testPublicHandle(): void
    {
        $this->expectException(\RuntimeException::class);

        SomeMethodWithPublicHandle::invoke();
    }
}