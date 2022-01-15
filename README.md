![Invoke](https://user-images.githubusercontent.com/21020331/145628046-ca19dbdf-2935-49fe-934c-a171219566cc.png)

PHP library for building fast and convenient web APIs.

## Installation

**The library is still work-in-progress.**

```shell
composer require storinka/invoke:^2
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
Invoke::setup(["add"]);

// run invoke to handle current http request
Invoke::serve();
```

2. Run a server

```shell
php -S localhost:8000 index.php 
```

3. Send a request

```shell
curl -X POST 'localhost:8000/invoke/add' --data '{ "a": 2, "b": 2 }'

// result will be: { "result": 4 }
```

## Complex example

1. Create a type

```php
use Invoke\Data;

class UserResult extends Data
{
    public int $id;
    
    public string $name;
}
```

2. Create a method to get list of users

```php
class GetUsers extends Method
{
    public int $page;
    
    public int $perPage;

    protected function handle(): array
    {
        $usersFromDB = getUsersFromDb($this->page, $this->perPage);
        
        return UserResult::many($usersFromDB);
    }
}
```

3. Setup Invoke

```php
Invoke::setup([
    "getUsers" => GetUsers::class,
]);

Invoke::serve();
```

4. Run a server and try to invoke as above:

```shell
curl -X POST 'localhost:8000/invoke/getUsers' --data '{ "page": 1, "perPage": 10 }'

// result will be: { "result": [] }
```
