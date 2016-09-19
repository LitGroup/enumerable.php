# Enumerable
> Library provides support of enumerable classes for PHP.

[![Version](https://img.shields.io/packagist/v/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![Dev Version](https://img.shields.io/packagist/vpre/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![Downloads](https://img.shields.io/packagist/dt/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)][license]
[![Build Status](https://travis-ci.org/LitGroup/enumerable.php.svg?branch=master)](https://travis-ci.org/LitGroup/enumerable.php)

* [Installation](#installation)
* [Example of usage](#example-of-usage)
  * [Definition](#definition)
  * [Equality/Identity checking](#equality-or-identity-checking)
  * [Usage in switch-case statement](#switch-case)
  * [Serialization and Persistence](#serialization-and-persistence)
  * [Extensibility](#extensibility)
* [Run tests](#run-tests)
* [License](#license)


## <a name="installation"></a>Installation
Install via composer:

```bash
composer require litgroup/enumerable:^0.4
```


## <a name="example-of-usage"></a>Example of usage
### <a name="definition"></a>Definition
1. Create `final` class, which extends `Enumerable`;
2. For each variant of values create a static method, which
   will creates an instance of value. For this purpose your method
   must call `Enumerable::createEnum()` with some index of value.

> **Note:**
> - Enumerable class must be `final`!
> - Index can be of type `string` or `int`.

**Enum definition example:**

```php
namespace Acme;

use LitGroup\Enumerable\Enumerable;

final class ColorEnum extends Enumerable
{
    /**
     * @return self
     */
    public static function red()
    {
        return self::createEnum('red');
    }

    /**
     * @return self
     */
    public static function green()
    {
        return self::createEnum('green');
    }

    /**
     * @return self
     */
    public static function blue()
    {
        return self::createEnum('blue');
    }
}
```

### <a name="equality-or-identity-checking"></a>Equality/Identity checking
You can use enumerable values in equality/identity expressions:

```php
ColorEnum::red() == ColorEnum::red() // => true
ColorEnum::red() === ColorEnum::red() // => true

ColorEnum::red() == ColorEnum::blue() // => false
ColorEnum::red() === ColorEnum::blue() // => false
```

> **Note:** Enumerables works as runtime constants. Therefor enumerable values can be
checked on **identity**. And we recommend to use check on identity (`===`) instesd of
equality (`==`) if possible.

### <a name="switch-case"></a>Usage in switch-case statement
```php
$color = ColorEnum::green();

switch ($color) {
    case ColorEnum::red():
        echo "Red!\n";
        break;
    case ColorEnum::green():
        echo "Green!\n";
        break;
    case ColorEnum::blue():
        echo "Blue!\n";
        break;
}

// "Green!" will be printed
```

### <a name="serialization-and-persistence"></a>Serialization and Persistence
`Enumerable` works as runtime-constant. Enumerable type cannot be serialized.
If you need to store representation of enumerable in a database or send
it via an API you can use index of enumerable value as representation.

```php
$enum->getIndex();
```

To restore an instance of enumerable type by index from database or
from API-request you can use static method `getValueOf()` on the concrete
enum-class.

```php
$colorIndex = getFromDatabase(/* something */);

$enum = ColorEnum::getValueOf($colorIndex);
```

If you need to get all values of enumerable type, use static method
`getValues()` on the concrete enum-class.

```php
ColorEnum::getValues(); // => Returns array of ColorEnum with index as key
```

### <a name="extensibility"></a>Extensibility
Instances of your enumerable classes can have additional behaviour if it needed.
But you cannot define any `public static` methods with behaviour. Public static
methods used only for creation of values.

> **Note:** You cannot define any `public static` methods with behaviour.
> Public static methods used only for creation of values.

**Example:**

```php
final class MergeRequestStatus extends Enumerable {

    public static function open()
    {
        return self::createEnum('open');
    }
    
    public static function approved()
    {
        return self::createEnum('approved');
    }

    public static function merged()
    {
        return self::createEnum('merged');
    }
    
    public static function declined()
    {
        return self::createEnum('declined');
    }
    
    /**
     * Returns true if status is final.
     *
     * @return bool
     */
    public function isFinal()
    {
        return $this === self::merged() || $this === self::declined();
    }
}
```

## <a name="run-tests"></a>Run tests
```bash
composer install
./tests.sh
```

## <a name="license"></a>LICENSE
See [LICENSE][license] file.

[license]: https://raw.githubusercontent.com/LitGroup/enumerable.php/master/LICENSE
