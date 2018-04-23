# Functional when-otherwise conditionals

[![Latest Version](https://img.shields.io/github/release/skollro/otherwise.svg?style=flat-square)](https://github.com/skollro/otherwise/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/skollro/otherwise/master.svg?style=flat-square)](https://travis-ci.org/skollro/otherwise)
[![StyleCI](https://styleci.io/repos/127410017/shield)](https://styleci.io/repos/127410017)

This package allows to replace PHP conditionals by an easy functional match-when-otherwise syntax.

```php
$result = match([1, 2, 3])
    ->when(function ($value) {
        return in_array(2, $value);
    }, '2 was found')
    ->otherwise('2 was not found');

// $result is "2 was found"
```

## Install

You can install this package via composer:

``` bash
composer require skollro/otherwise
```

## Usage

Every conditional `match` consists out of one or multiple `when` and one `otherwise` to provide values for each path.

#### `match($value, ...$params): Match`

This package provides a helper function `match`. The first value is the value to match against. You can pass a variable amount of `$params` which are passed to every callable that resolves a `$result`.

```php
use Skollro\Otherwise\Match;
use function Skollro\Otherwise\match;

$match = match($value);
$match = Match::value($value);
```

#### `when($condition, $result): Match`

`$condition` is a bool, a callable or a value to compare against (using `==`). `$result` takes either some value or a callable for lazy evaluation. More specific conditions have to be defined first because the first match is the final result.

```php
$result = match('A', 'Some value')
    ->when('B', function ($value) {
        return "{$value} is always false: A != B";
    })
    ->when(true, function ($value, $param) {
        return "This is always true ({$param})";
    })
    ->when(function ($value) {
        return strlen($value) == 1;
    }, 'This is not the first match')
    ->otherwise('B');

// $result is "This is always true (Some value)" because it's the first condition that evaluates to true
```

#### `whenInstanceOf($type, $result): Match`

This is just a shortcut method for `$value instanceof A`. `$type` is anything that can be on the left side of an `instanceof` operator. `$result` takes either some value or a callable for lazy evaluation. More specific conditions have to be defined first because the first match is the final result.

```php
$result = match(new A)
    ->whenInstanceOf(B::class, 'This is false')
    ->whenInstanceOf(A::class, 'This is true')
    ->when(function ($value) {
        return $value instanceof A;
    }, 'This is not the first match')
    ->otherwise('C');

// $result is "This is true" because it's the first condition that evaluates to true
```

#### `whenThrow($condition, $result): Match`

`$condition` is a bool, a callable or a value to compare against (using `==`). `$result` takes either an exception class name, an exception instance or a callable that returns an exception. More specific conditions have to be defined first because the first match throws the exception instantly.

```php
$result = match('A')
    ->when(false, 'This is always false')
    ->whenThrow('A', Exception::class)
    ->otherwise('C');

// Exception is thrown
```

#### `otherwise($value)`

`$value` is of type callable or some value. Supplies the default value if no `when` has evaluated to `true` before.

```php
$result = match('A')
    ->when(false, 'This is always false')
    ->otherwise('B');

// $result is "B"

$result = match('A', 'Some value')
    ->when(false, 'This is always false')
    ->otherwise(function ($value, $param) {
        return "{$value} ({$param})";
    });

// $result is "A (Some value)"

$result = match('A')
    ->when(false, 'This is always false')
    ->otherwise('strlen');

// $result is 1
```

#### `otherwiseThrow($value)`

Throws an exception if no `when` has evaluated to `true` before. It takes an exception class name, an exception instance or a callable that returns an exception.

```php
// recommended: an instance of the exception is only created if needed
$result = match('A')
    ->when(false, 'This is always false')
    ->otherwiseThrow(Exception::class);

$result = match('A', 'Some value')
    ->when(false, 'This is always false')
    ->otherwiseThrow(function ($value, $param) {
        throw new Exception("Message {$value} ({$param})");
    });

// not recommended
$result = match('A')
    ->when(false, 'This is always false')
    ->otherwiseThrow(new Exception);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
