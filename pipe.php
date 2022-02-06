<?php
//
//use Invoke\Data;
//use Invoke\Invoke;
//use Invoke\Method;
//use Invoke\Schema\SchemaDocument;
//use Invoke\Types\File;
//use Invoke\Validations\ArrayOf;
//
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//
//require "vendor/autoload.php";
//
//if (isset($_SERVER["HTTP_ORIGIN"])) {
//    header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
//    header("Access-Control-Allow-Credentials: true");
//    header("Access-Control-Max-Age: 86400");
//}
//
//if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "OPTIONS") {
//    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) {
//        header("Access-Control-Allow-Methods: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]}");
//    }
//
//    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
//        header("Access-Control-Allow-Headers: {$_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}");
//    }
//
//    exit(0);
//}
//
///**
// * Some data type
// */
//class SomeData extends Data
//{
//    public int $dig;
//
//    public AnotherData $anotherData;
//}
//
//class AnotherData extends Data
//{
//    public string $rofl;
//}
//
///**
// * Get list of users.
// *
// * Some very <b>long</b> method <i>description</i>.
// */
//#[SomeMethodExtension]
//class SomeMethod extends Method
//{
//    public string $name;
//
//    public int $age;
//
//    public bool $isActive;
//
//    #[ArrayOf([
//        "int",
//        "string",
//        SomeData::class,
//        "bool",
//        new ArrayOf("string")
//    ])]
//    public array $items;
//
//    public SomeData $data;
//
//    public $notTyped;
//
//    public float $latitude;
//
//    public int|string $mixed;
//
//    public ?File $optionalFile;
//
//    // todo: support object type
////    public object $obj;
//
//    protected function handle(): SomeData
//    {
//        return $this->data;
//    }
//}
//
//class User extends Data
//{
//    public int $id;
//
//    public string $name;
//}
//
//class GetUsers extends Method
//{
//    public int $page;
//
//    public function handle(): array
//    {
//        return User::many([
//            [
//                "id" => 123,
//                "name" => "Mi",
//            ],
//            [
//                "id" => 10,
//                "name" => "Vi"
//            ]
//        ]);
//    }
//}
//
//class GetSchema extends Method
//{
//    protected function handle(): SchemaDocument
//    {
//        return SchemaDocument::current();
//    }
//}
//
//Invoke::setup([
//    GetUsers::class,
//    GetSchema::class,
//    SomeMethod::class,
//]);
//
//Invoke::serve();
