![Invoke](https://user-images.githubusercontent.com/21020331/145628046-ca19dbdf-2935-49fe-934c-a171219566cc.png)


JSON-RPC compliant library for building fast and convenient web APIs.

## Installation

**The library is still work-in-progress.**

```shell
composer require storinka/invoke
```

## Basic example

1. Create index.php

```php
// define some function
function add(float $a, float $b): float
{
    return $a + $b;
}

// setup invoke providing map of functions
Invoke::setup([
    "add" => "add",
]);

// run invoke to handle current http request
Invoke::handleJSONRPCRequest();
```

2. Run a server

```shell
php -S localhost:8000 index.php 
```

3. Send a request

```shell
curl -X POST 'localhost:8000' --data '{ "method": "add", "params": { "a": 2, "b": 2 } }'

// result will be: { "result": 4 }
```

## Complex example

1. Create a type
```php
class UserResult extends Result
{
    public int $id;
    
    public string $name;
}
```

2. Create a method to get list of users
```php
class GetUsers extends Method
{
    public static function params(): array
    {
        return [
            "id" => int(),
            "perPage" => int(1, 100), // min 1, max 100
        ];
    }

    protected function handle(int $page, int $perPage): array
    {
        $usersFromDB = /* fetch users from db */;
        
        return UserResult::many($usersFromDB);
    }
}
```

3. Setup Invoke
```php
Invoke::setup([
    "getUsers" => GetUsers::class,
]);

Invoke::handleJSONRPCRequest();
```

4. Run a server and try to invoke as above
