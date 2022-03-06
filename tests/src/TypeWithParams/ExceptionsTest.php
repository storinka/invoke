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
use InvokeTests\TypeWithParams\Fixtures\SomeTypeWithParams;
use InvokeTests\TypeWithParams\Fixtures\TypeWithMixedTypeProperty;
use InvokeTests\TypeWithParams\Fixtures\TypeWithValidator;
use InvokeTests\TypeWithParams\Fixtures\Validators\Exceptions\SomeValidatorException;

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
    public function testInvalidBasics($input, $exceptionClass, ...$exceptionParams)
    {
        $this->expectExceptionObject(new $exceptionClass(...$exceptionParams));

        Piping::run(new SomeTypeWithParams(), $input);
    }

    protected function invalidMixedProvider(): array
    {
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
    public function testInvalidMixed($input, $exceptionClass, ...$exceptionParams)
    {
        $this->expectExceptionObject(new $exceptionClass(...$exceptionParams));

        Piping::run(new TypeWithMixedTypeProperty(), $input);
    }

    public function testInvalidValidator()
    {
        $this->expectExceptionObject(new SomeValidatorException);

        Piping::run(new TypeWithValidator(), ["string" => "fail"]);
    }
}