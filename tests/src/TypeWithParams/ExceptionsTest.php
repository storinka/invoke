<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Exceptions\InvalidParameterTypeException;
use Invoke\Exceptions\ParameterTypeNameRequiredException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Piping;
use Invoke\Types\StringType;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\SomeType;
use InvokeTests\TypeWithParams\Fixtures\TypeWithMixedTypeProperty;

class ExceptionsTest extends TestCase
{
    protected function invalidBasicsProvider(): array
    {
        return [
            [[], RequiredParameterNotProvidedException::class, "name"],
            [["name" => "Davyd"], RequiredParameterNotProvidedException::class, "intWithPipe"],
            [["name" => null], InvalidParameterTypeException::class, "name", StringType::getInstance(), "null"],
        ];
    }

    /**
     * @dataProvider invalidBasicsProvider
     */
    public function testInvalidBasics($input, $exceptionClass, ...$exceptionParams){
        $this->expectExceptionObject(new $exceptionClass(...$exceptionParams));

        Piping::run(new SomeType(), $input);
    }

    protected function invalidMixedProvider(): array{
        return [
            [[], RequiredParameterNotProvidedException::class, "mixedSomeType"],
            [["mixedSomeType" => []], ParameterTypeNameRequiredException::class, "mixedSomeType"],
            [["mixedSomeType" => ["@type" => "AnotherSomeType"]], RequiredParameterNotProvidedException::class, "mixedSomeType->numeric"]
        ];
    }
    /**
     * @dataProvider invalidMixedProvider
     */
    public function testInvalidMixed($input, $exceptionClass, ...$exceptionParams){
        $this->expectExceptionObject(new $exceptionClass(...$exceptionParams));

        Piping::run(new TypeWithMixedTypeProperty(), $input);
    }
}