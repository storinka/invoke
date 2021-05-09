<?php

use Invoke\Typesystem\Exceptions\TypesystemValidationException;
use InvokeTests\UndefAndNullInput;
use PHPUnit\Framework\TestCase;

final class UndefAndNullInputTest extends TestCase
{
    public function testUndefInput1()
    {
        $input = new UndefAndNullInput([
            "undefOrInt" => 100,
            "nullOrInt" => null,
        ]);

        $this->assertArrayHasKey("undefOrInt", $input->getValidatedAttributes());
        $this->assertArrayHasKey("nullOrInt", $input->getValidatedAttributes());
        $this->assertEquals(100, $input->get("undefOrInt"));
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

        $this->assertArrayNotHasKey("undefOrInt", $input3->getValidatedAttributes());
        $this->assertArrayHasKey("nullOrInt", $input3->getValidatedAttributes());
        $this->assertEquals(200, $input3->get("nullOrInt"));
    }

    public function testUndefInput4()
    {
        $input4 = new UndefAndNullInput([
            "undefOrInt" => 100,
        ]);

        $this->assertArrayHasKey("undefOrInt", $input4->getValidatedAttributes());
        $this->assertArrayHasKey("nullOrInt", $input4->getValidatedAttributes());
        $this->assertEquals(100, $input4->get("undefOrInt"));
        $this->assertEquals(null, $input4->get("nullOrInt"));
    }
}
