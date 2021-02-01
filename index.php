<?php

error_reporting(E_ALL);

use Invoke\InvokeMachine;
use Invoke\Local\Dec2Hex;

include "vendor/autoload.php";

try {
    InvokeMachine::setup([
        0 => [
            "dec2hex" => Dec2Hex::class,
        ],
        1 => [
            "hex2dec" => null,
        ],
        2 => [
            "dec2hex" => null,
        ],
    ], [
        "strict" => false,
    ]);

    $functionName = trim(trim(trim($_SERVER["PATH_INFO"]), "/"));
    $inputParams = $_SERVER["REQUEST_METHOD"] === "POST" ? json_decode(file_get_contents("php://input"), true) : $_GET;

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        echo "<pre>";
    }

    print_r(InvokeMachine::invoke($functionName, $inputParams, 1));
} catch (Throwable $e) {
    print_r($e);
}
