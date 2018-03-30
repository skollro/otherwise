# Functional when-otherwise conditionals

[![Latest Version](https://img.shields.io/github/release/skollro/otherwise.svg?style=flat-square)](https://github.com/skollro/otherwise/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/skollro/otherwise/master.svg?style=flat-square)](https://travis-ci.org/skollro/otherwise)
[![StyleCI](https://styleci.io/repos/127410017/shield)](https://styleci.io/repos/127410017)
[![Total Downloads](https://img.shields.io/packagist/dt/skollro/otherwise.svg?style=flat-square)](https://packagist.org/packages/skollro/otherwise)

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

### Helper function

This package provides a helper function `match`. It returns an instance of `Skollro\Otherwise\Match`.

```php
use Skollro\Otherwise\Match;
use function Skollro\Otherwise\match;

$match = match($value);
$match = Match::value($value);
```

### When

`when($condition, $result)` accepts callbacks, function names or bools as `$condition`. `$result` takes either a value or a callback for lazy evaluation. More specific conditions have to be defined first because the first match is considered to be the result.

```php
$result = match('A')
    ->when(false, function ($value) {
        return "This {$value} is always false";
    })
    ->when(true, 'This is always true')
    ->when(function ($value) {
        return strlen($value) == 1;
    }, 'This is not the first match')
    ->otherwise('B');

// $result is "This is always true" because it's the first condition that evaluates to true
```

### Otherwise

`otherwise($value)` accepts callbacks, function names or values. Supplies the default value if no `when` has evaluated to `true` before.

```php
$result = match('A')
    ->when(false, 'This is always false')
    ->otherwise('B');

// $result is "B"

$result = match('A')
    ->when(false, 'This is always false')
    ->otherwise(function ($value) {
        return $value;
    });

// $result is "A"

$result = match('A')
    ->when(false, 'This is always false')
    ->otherwise('strlen');

// $result is 1
```

### Otherwise Throw

`otherwiseThrow($value)` throws an exception if no `when` has evaluated to `true`
 before. It accepts exception class names, exception objects or a callback that returns an exception.

```php
// recommended: an instance of the exception is only created if needed
$result = match('A')
    ->when(false, 'This is always false')
    ->otherwiseThrow(Exception::class);

$result = match('A')
    ->when(false, 'This is always false')
    ->otherwiseThrow(function ($value) {
        throw new Exception("Message {$value}");
    });

// not recommended
$result = match('A')
    ->when(false, 'This is always false')
    ->otherwiseThrow(new Exception);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
