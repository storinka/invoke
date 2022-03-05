<?php

namespace InvokeTests;

use Invoke\Exceptions\InvalidParameterTypeException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Types\StringType;
use InvokeTests\Data\Fixtures\SomeData;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class DataTest extends TestCase
{
    protected function assertSomeData(SomeData $data, array $input)
    {
        foreach ($input as $key => $val) {
            if ($key === 'intWithPipe') {
                $val = $val * 2;
            }

            assertEquals($data->{$key}, $val);
        }
    }

    public function testValid()
    {
        $input = [
            "name" => "Davyd",
            "intWithPipe" => 2
        ];
        $data = SomeData::from($input);

        $this->assertSomeData($data, $input);
    }

    public function testValidNullable()
    {
        $data = SomeData::nullable(null);
        assertNull($data);

        $data = SomeData::nullable([
            "name" => "Davyd",
            "intWithPipe" => 2
        ]);
        assertNotNull($data);
    }

    public function testValidMany()
    {
        $items = [];

        for ($i = 0; $i < 10; $i++) {
            $input = [
                "name" => "name" . $i,
                "intWithPipe" => $i + 10,
            ];
            $items[$i] = $input;
        }

        $someDataItems = SomeData::many($items);

        foreach ($someDataItems as $i => $item) {
            $this->assertSomeData($item, $items[$i]);
        }
    }

    protected function invalidProvider(): array
    {
        return [
            [[], RequiredParameterNotProvidedException::class, "name"],
            [["name" => "Davyd"], RequiredParameterNotProvidedException::class, "intWithPipe"],
            [["name" => null], InvalidParameterTypeException::class, "name", StringType::getInstance(), "null"],
        ];
    }

    /**
     * @dataProvider invalidProvider
     */
    public function testInvalid($input, $exceptionClass, ...$exceptionParams)
    {
        $this->expectExceptionObject(new $exceptionClass(...$exceptionParams));

        SomeData::from($input);
    }
}