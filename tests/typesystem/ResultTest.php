<?php

use InvokeTests\AllBasicTypesResult;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    public function testGeneralResultShouldNotFail()
    {
        $result = AllBasicTypesResult::from([
            "T" => new RuntimeException(),
            "bool" => true,
            "int" => 256,
            "float" => 256.512,
            "string" => "love is a trap",
            "null" => null,
            "array" => [2, 0, 2, 0],
        ]);

        $validatedAttributes = $result->getValidatedParams();

        $this->assertArrayHasKey("T", $validatedAttributes);
        $this->assertArrayHasKey("bool", $validatedAttributes);
        $this->assertArrayHasKey("int", $validatedAttributes);
        $this->assertArrayHasKey("float", $validatedAttributes);
        $this->assertArrayHasKey("string", $validatedAttributes);
        $this->assertArrayHasKey("null", $validatedAttributes);
        $this->assertArrayHasKey("array", $validatedAttributes);
    }
}
