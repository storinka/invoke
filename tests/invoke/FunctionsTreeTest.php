<?php
//
//namespace InvokeTests\Invoke;
//
//use Invoke\Exceptions\InvalidFunctionException;
//use Invoke\Exceptions\InvalidVersionException;
//use Invoke\InvokeMachine;
//use PHPUnit\Framework\TestCase;
//
//class FunctionsTreeTest extends TestCase
//{
//    public function testBasicFunctionRegistration()
//    {
//        Invok::setup([
//            1 => [
//                "dechex" => "dechex"
//            ]
//        ]);
//
//        $this->assertEquals("dechex", InvokeMachine::getFunctionClass("dechex"));
//    }
//
//    public function testMultipleVersionsFunctionRegistration()
//    {
//        InvokeMachine::setup([
//            1 => [
//                "dechex" => "dechex"
//            ],
//            2 => [
//                "dechex" => "dechex2"
//            ],
//        ]);
//
//        // v1
//        $this->assertEquals("dechex", InvokeMachine::getFunctionClass("dechex", 1));
//
//        // v2
//        $this->assertEquals("dechex2", InvokeMachine::getFunctionClass("dechex", 2));
//    }
//
//    public function testVersionHasPreviousVersionFunctions()
//    {
//        InvokeMachine::setup([
//            1 => [
//                "dechex" => "dechex"
//            ],
//            2 => [
//                "dechex2" => "dechex2"
//            ],
//            3 => [
//                "dechex3" => "dechex3"
//            ],
//        ]);
//
//        // v1
//        $this->assertEquals("dechex", InvokeMachine::getFunctionClass("dechex", 1));
//
//        // v2
//        $this->assertEquals("dechex", InvokeMachine::getFunctionClass("dechex", 2));
//        $this->assertEquals("dechex2", InvokeMachine::getFunctionClass("dechex2", 2));
//
//        // v3
//        $this->assertEquals("dechex", InvokeMachine::getFunctionClass("dechex", 3));
//        $this->assertEquals("dechex2", InvokeMachine::getFunctionClass("dechex2", 3));
//        $this->assertEquals("dechex3", InvokeMachine::getFunctionClass("dechex3", 3));
//    }
//
//    public function setupTestRemoveFunction()
//    {
//        InvokeMachine::setup([
//            1 => [
//                "dechex" => "dechex"
//            ],
//            2 => [
//                "dechex" => null,
//                "dechex2" => "dechex2",
//            ],
//            3 => [
//                "dechex2" => null,
//                "dechex3" => "dechex3",
//            ],
//        ]);
//    }
//
//    public function testRemoveFunctionV1()
//    {
//        $this->setupTestRemoveFunction();
//
//        // v1
//        $this->assertEquals("dechex", InvokeMachine::getFunctionClass("dechex", 1));
//    }
//
//    public function testRemoveFunctionV2()
//    {
//        $this->setupTestRemoveFunction();
//
//        // v2
//        $this->assertEquals("dechex2", InvokeMachine::getFunctionClass("dechex2", 2));
//
//        $this->expectException(InvalidFunctionException::class);
//        $this->expectExceptionMessage("Invalid function \"dechex\".");
//        $this->assertEquals(null, InvokeMachine::getFunctionClass("dechex", 2));
//    }
//
//    public function testRemoveFunctionV3_1()
//    {
//        $this->setupTestRemoveFunction();
//
//        // v3
//        $this->assertEquals("dechex3", InvokeMachine::getFunctionClass("dechex3", 3));
//
//        $this->expectException(InvalidFunctionException::class);
//        $this->expectExceptionMessage("Invalid function \"dechex\".");
//        $this->assertEquals(null, InvokeMachine::getFunctionClass("dechex", 3));
//    }
//
//    public function testRemoveFunctionV3_2()
//    {
//        $this->setupTestRemoveFunction();
//
//        // v3
//        $this->expectException(InvalidFunctionException::class);
//        $this->expectExceptionMessage("Invalid function \"dechex2\".");
//        $this->assertEquals("dechex2", InvokeMachine::getFunctionClass("dechex2", 3));
//    }
//
//    public function testCurrentVersionIsValid()
//    {
//        // 1
//        InvokeMachine::setup([
//            1 => []
//        ]);
//        $this->assertEquals("1.0.0", InvokeMachine::version());
//
//        // 2
//        InvokeMachine::setup([
//            1 => [],
//            2 => [],
//        ]);
//        $this->assertEquals("2.0.0", InvokeMachine::version());
//
//        // 3
//        InvokeMachine::setup([
//            1 => [],
//            2 => [],
//            "2.5" => []
//        ]);
//        $this->assertEquals("2.5.0", InvokeMachine::version());
//
//        // 4
//        InvokeMachine::setup([
//            1 => [],
//            2 => [],
//            "2.5" => [],
//            "2.5.6" => []
//        ]);
//        $this->assertEquals("2.5.6", InvokeMachine::version());
//
//        // 5
//        InvokeMachine::setup([
//            1 => [],
//            2 => [],
//            "2.5" => [],
//            "2.5.6" => [],
//            "3" => []
//        ]);
//        $this->assertEquals("3.0.0", InvokeMachine::version());
//    }
//
//    public function setupTestClosestVersionIsOk()
//    {
//        InvokeMachine::setup([
//            1 => [],
//            2 => [],
//            "2.5" => [],
//            "2.5.6" => [],
//            "3" => [],
//            "4" => [],
//            "4.1" => [],
//        ]);
//    }
//
//    public function testClosestVersionIsOk_1()
//    {
//        $this->setupTestClosestVersionIsOk();
//
//        $this->assertEquals("2.5.6", InvokeMachine::getClosestVersion("2"));
//        $this->assertEquals("2.5.6", InvokeMachine::getClosestVersion("2.5"));
//        $this->assertEquals("2.5.6", InvokeMachine::getClosestVersion("2.5.6"));
//
//        $this->assertEquals("3.0.0", InvokeMachine::getClosestVersion("3"));
//        $this->assertEquals("3.0.0", InvokeMachine::getClosestVersion("3.0"));
//        $this->assertEquals("3.0.0", InvokeMachine::getClosestVersion("3.0.0"));
//
//        $this->assertEquals("4.1.0", InvokeMachine::getClosestVersion("4"));
//        $this->assertEquals("4.0.0", InvokeMachine::getClosestVersion("4.0"));
//        $this->assertEquals("4.0.0", InvokeMachine::getClosestVersion("4.0.0"));
//        $this->assertEquals("4.1.0", InvokeMachine::getClosestVersion("4.1"));
//    }
//
//    public function testClosestVersionIsOk_2()
//    {
//        $this->setupTestClosestVersionIsOk();
//
//        $this->expectException(InvalidVersionException::class);
//        InvokeMachine::getClosestVersion("2.5.7");
//    }
//
//    public function testClosestVersionIsOk_3()
//    {
//        $this->setupTestClosestVersionIsOk();
//
//        $this->expectException(InvalidVersionException::class);
//        InvokeMachine::getClosestVersion("3.0.1");
//    }
//}