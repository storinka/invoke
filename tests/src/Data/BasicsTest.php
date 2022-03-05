<?php

namespace InvokeTests\Data;

use InvokeTests\Data\Fixtures\SomeData;
use InvokeTests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class BasicsTest extends TestCase
{
    protected function assertSomeData(SomeData $data, array $input)
    {
        foreach ($input as $key => $val) {
            assertEquals($data->{$key}, $val);
        }
    }

    public function testFrom()
    {
        $input = [
            "name" => "Davyd",
        ];
        $data = SomeData::from($input);

        $this->assertSomeData($data, $input);
    }

    public function testNullable()
    {
        $data = SomeData::nullable(null);
        assertNull($data);

        $data = SomeData::nullable([
            "name" => "Davyd",
        ]);
        assertNotNull($data);
    }

    public function testMany()
    {
        $items = [];

        for ($i = 0; $i < 10; $i++) {
            $input = [
                "name" => "name" . $i,
            ];
            $items[$i] = $input;
        }

        $someDataItems = SomeData::many($items);

        foreach ($someDataItems as $i => $item) {
            $this->assertSomeData($item, $items[$i]);
        }
    }
}