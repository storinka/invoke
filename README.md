# Storinka Invoke

Brand-new way of creating web API (at least for me).

## Installation

```shell
composer require storinka/invoke
```

## Basic usage

```php
// UserResult.php

use Invoke\Typesystem\Result;
use Invoke\Typesystem\Type;

class UserResult extends Result
{
    public static function params() : array
    {
        return [
            "id" => Type::Int,
            "name" => Type::String,
            "email" => Type::String,
        ];
    }
}
```

```php
// GetUser.php

use Invoke\InvokeFunction;
use Invoke\Typesystem\Type;

class GetUser extends InvokeFunction
{
    public static function params() : array
    {
         return [
            "id" => Type::Int,
         ];
    }

    protected function handle(array $params): UserResult
    {
        $user = getUserFromDb($params["id"]);
        
        return UserResult::create($user);
    }
}
```

```php
// index.php

use Invoke\InvokeMachine;

$functionName = trim(trim(trim($_SERVER["PATH_INFO"]), "/"));
$inputParams = $_REQUEST;

InvokeMachine::setup([
    0 => [
        "getUser" => GetUser::class,
    ],
], [
    "strict" => false,
]);

$result = InvokeMachine::invoke($functionName, $inputParams, 0);

print_r($result);

// curl localhost:9000/getUser?id=1
```
