<?php

use Invoke\V1\Typesystem\Types;
use InvokeTests\AllBasicTypesResult;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    public function testGeneralResultShouldNotFail()
    {
        $result = AllBasicTypesResult::create([
            "T" => new RuntimeException(),
            "bool" => true,
            "int" => 256,
            "float" => 256.512,
            "string" => "love is a trap",
            "null" => null,
            "array" => [2, 0, 2, 0],
//            "nullOrInt" => "should fail"
        ]);

        $validatedAttributes = $result->getValidatedParams();

        $this->assertArrayHasKey("T", $validatedAttributes);
        $this->assertArrayHasKey("bool", $validatedAttributes);
        $this->assertArrayHasKey("int", $validatedAttributes);
        $this->assertArrayHasKey("float", $validatedAttributes);
        $this->assertArrayHasKey("string", $validatedAttributes);
        $this->assertArrayHasKey("null", $validatedAttributes);
        $this->assertArrayHasKey("array", $validatedAttributes);

        $result = \Invoke\V1\Typesystem\TypesystemV1::validateParams([
            "integers" => Types::ArrayOf(Types::Int, 0, 10)
        ], [
            "integers" => [1, 2, 3, 4]
        ]);

        print_r($result);
    }
}
