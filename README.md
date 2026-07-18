# Enumerable

This library was developed to support enum types before PHP 8.1.

Since release `0.9.0` of the library it provides API similar to current
PHP 8 backed enum types. Previous API is marked as deprecated.
This change was made intentionally to support migration to native enums
in PHP 8.

[![Version](https://img.shields.io/packagist/v/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![Downloads](https://img.shields.io/packagist/dt/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

- - -

- [Enumerable](#enumerable)
  - [Installation](#installation)
  - [Example of usage](#example-of-usage)
    - [Definition](#definition)
    - [Equality/Identity checking](#equalityidentity-checking)
    - [Usage in switch-case statement](#usage-in-switch-case-statement)
    - [Serialization and Persistence](#serialization-and-persistence)
    - [Extensibility](#extensibility)
  - [Run tests](#run-tests)
  - [LICENSE](#license)


## <a name="installation"></a>Installation
Install via composer:

```bash
composer require litgroup/enumerable:^0.9.0
```


## <a name="example-of-usage"></a>Example of usage
### <a name="definition"></a>Definition
1. Create `final` class, which extends `Enumerable`;
2. For each variant of values create a static method, which
   will creates an instance of value. For this purpose your method
   must call `Enumerable::case()` with some backed value.

> **Note:**
> - Enumerable class must be `final`!
> - Backed can be ether `string` or `int`.

**Enum definition example:**

```php
namespace Acme;

use LitGroup\Enumerable\Enumerable;

final class ColorEnum extends Enumerable
{
    public static function red(): self
    {
        return self::case('red');
    }

    public static function green(): self
    {
        return self::case('green');
    }

    public static function blue(): self
    {
        return self::case('blue');
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

> **Note:** Enumerables works as runtime constants. Therefore enumerable values can be
checked on **identity**. And we recommend to use check on identity (`===`) instead of
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

A `match` expression also works es expected.

### <a name="serialization-and-persistence"></a>Serialization and Persistence
`Enumerable` works as runtime-constant. Enumerable type cannot be serialized.
If you need to store representation of enumerable in a database or send
it via an API you can use a backed value of enumerable as its representation.

```php
$enum->value;
```

To restore an instance of enumerable type by its backed value from database or
from API-request you can use static method `tryFrom()` on the concrete
enum-class.

```php
$colorRawValue = fetchValueFromDatabase(/* something */);

$enum = ColorEnum::tryFrom($colorRawValue);
```

If you need to get all values of enumerable type, use static method
`cases()` on the concrete enum-class.

```php
ColorEnum::cases(); // => Returns a list of enum cases
```

### <a name="extensibility"></a>Extensibility
Instances of your enumerable classes can have additional behavior if it needed.
But you cannot define any `public static` methods with custom behavior.
Public static methods used only for initialization of the enum.

> **Note:** You cannot define any `public static` methods with custom behavior.
> Public static methods used only for initialization of the enum.

**Example:**

```php
final class MergeRequestStatus extends Enumerable {

    public static function open(): self
    {
        return self::case('open');
    }

    public static function approved(): self
    {
        return self::case('approved');
    }

    public static function merged(): self
    {
        return self::case('merged');
    }

    public static function declined(): self
    {
        return self::case('declined');
    }

    /**
     * Returns true if status is final.
     */
    public function isFinal(): bool
    {
        return $this === self::merged() || $this === self::declined();
    }
}
```

## <a name="run-tests"></a>Run tests
```bash
composer install
composer test
```

## <a name="license"></a>LICENSE
See [LICENSE](LICENSE) file.
