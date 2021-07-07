<?php

use Invoke\Typesystem\Exceptions\TypesystemValidationException;
use InvokeTests\UndefAndNullInput;

final class UndefAndNullInputTest
{
    public function testUndefInput1()
    {
        $input = new UndefAndNullInput([
            "undefOrInt" => 100,
            "nullOrInt" => null,
        ]);

        $params = $input->getValidatedParams();

        $this->assertArrayHasKey("undefOrInt", $params);
        $this->assertArrayHasKey("nullOrInt", $params);
        $this->assertEquals(100, $params["undefOrInt"]);
    }

    public function testUndefInput2()
    {
        $this->expectException(TypesystemValidationException::class);
        $this->expectExceptionMessage("Invalid \"undefOrInt\" type: expected \"Undef | Int\", got \"Null\".");

        $input2 = new UndefAndNullInput([
            "undefOrInt" => null,
            "nullOrInt" => null,
        ]);
    }

    public function testUndefInput3()
    {
        $input3 = new UndefAndNullInput([
            "nullOrInt" => 200,
        ]);

        $params = $input3->getValidatedParams();

        $this->assertArrayHasKey("nullOrInt", $params);
        $this->assertEquals(200, $params["nullOrInt"]);
    }

    public function testUndefInput4()
    {
        $input4 = new UndefAndNullInput([
            "undefOrInt" => 100,
        ]);

        $params = $input4->getValidatedParams();

        $this->assertArrayHasKey("undefOrInt", $params);
        $this->assertArrayHasKey("nullOrInt", $params);
        $this->assertEquals(100, $params["undefOrInt"]);
        $this->assertEquals(null, $params["nullOrInt"]);
    }
}
