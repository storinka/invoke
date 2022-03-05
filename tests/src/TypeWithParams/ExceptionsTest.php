<?php

namespace InvokeTests\TypeWithParams;

use Invoke\Exceptions\InvalidParameterTypeException;
use Invoke\Exceptions\ParameterTypeNameRequiredException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Piping;
use Invoke\Types\StringType;
use Invoke\Types\UnionType;
use InvokeTests\TestCase;
use InvokeTests\TypeWithParams\Fixtures\AnotherAnotherSomeType;
use InvokeTests\TypeWithParams\Fixtures\AnotherSomeType;
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
        $typeMixedSomeType = new UnionType([AnotherSomeType::class, AnotherAnotherSomeType::class]);

        return [
            [[], RequiredParameterNotProvidedException::class, "mixedSomeType"],
            [["mixedSomeType" => []], ParameterTypeNameRequiredException::class, "mixedSomeType"],
            [["mixedSomeType" => ["@type" => "lol123"]], InvalidParameterTypeException::class, "mixedSomeType", $typeMixedSomeType, "lol123"],
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