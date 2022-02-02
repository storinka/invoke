<?php

namespace InvokeTests\Typesystem;

use InvokeTests\Lib\AllBasicTypesType;
use InvokeTests\Lib\SetupInvoke;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class TypeTest extends TestCase
{
    use SetupInvoke;

    public function testGeneralResultShouldNotFail()
    {
        $result = AllBasicTypesType::from([
            "T" => new RuntimeException(),
            "bool" => true,
            "int" => 256,
            "float" => 256.512,
            "string" => "love is a trap",
            "array" => [2, 0, 2, 0],

            "notType" => [
                "id" => 1,
                "name" => "some name",

                "nestedNotType" => [
                    "is_active" => true
                ],
            ],
        ]);

        $validatedAttributes = $result->toDataArray();

        $this->assertArrayHasKey("T", $validatedAttributes);
        $this->assertArrayHasKey("bool", $validatedAttributes);
        $this->assertArrayHasKey("int", $validatedAttributes);
        $this->assertArrayHasKey("float", $validatedAttributes);
        $this->assertArrayHasKey("string", $validatedAttributes);
//        $this->assertArrayHasKey("null", $validatedAttributes);
        $this->assertArrayHasKey("array", $validatedAttributes);
//        $this->assertArrayHasKey("notType", $validatedAttributes);
    }
}
