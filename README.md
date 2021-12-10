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
function add(float $a, float $b): float
{
    return $a + $b;
}

Invoke::setup([
    "add" => "add",
]);

Invoke::handleRequest();
```

2. Run a server

```shell
pgp -S localhost:8000 index.php 
```

3. Send a request

```shell
curl -X POST 'localhost:8000/add' --data '{ "a": 2, "b": 2 }'

// result will be: { "result": 4 }
```
