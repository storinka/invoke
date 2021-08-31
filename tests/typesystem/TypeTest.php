<?php

use InvokeTests\AllBasicTypesType;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    public function testGeneralResultShouldNotFail()
    {
        $result = AllBasicTypesType::from([
            "T" => new RuntimeException(),
            "bool" => true,
            "int" => 256,
            "float" => 256.512,
            "string" => "love is a trap",
            "null" => null,
            "array" => [2, 0, 2, 0],

            "notType" => [
                "id" => 1,
                "name" => "some name",

                "nestedNotType" => [
                    "is_active" => true
                ],
            ],
        ]);

        $validatedAttributes = $result->getValidatedParams();

        $this->assertArrayHasKey("T", $validatedAttributes);
        $this->assertArrayHasKey("bool", $validatedAttributes);
        $this->assertArrayHasKey("int", $validatedAttributes);
        $this->assertArrayHasKey("float", $validatedAttributes);
        $this->assertArrayHasKey("string", $validatedAttributes);
        $this->assertArrayHasKey("null", $validatedAttributes);
        $this->assertArrayHasKey("array", $validatedAttributes);
        $this->assertArrayHasKey("notType", $validatedAttributes);
    }
}
