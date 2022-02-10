![Invoke](https://user-images.githubusercontent.com/21020331/145628046-ca19dbdf-2935-49fe-934c-a171219566cc.png)

PHP library for building fast and modern web APIs.

- #### [Explore](https://explore.invoke.red)
- #### [Documentation](https://invoke.red/docs)

## Installation

**The library is still work-in-progress.**

```shell
composer require storinka/invoke:^2
```

## Basic example

1. Create `index.php`

```php
use Invoke\Invoke;

function add(float $a, float $b): float
{
    return $a + $b;
}

Invoke::setup(["add"]);

Invoke::serve();
```

2. Run a server

```shell
php -S localhost:8000 index.php 
```

3. Make a request

```shell
curl 'localhost:8000/add?a=2&b=2'

# { "result": 4 }
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
use Invoke\Method;

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
use Invoke\Invoke;

Invoke::setup([
    "getUsers" => GetUsers::class,
]);

Invoke::serve();
```

4. Run a server and try to invoke:

```shell
curl 'localhost:8000/getUsers?page=1&perPage=10'

# { "result": [ ... ] }
```
