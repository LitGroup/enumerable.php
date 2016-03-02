Enumerable
==========

Library provides support of enumerable classes for PHP.

[![Version](https://img.shields.io/packagist/v/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![Dev Version](https://img.shields.io/packagist/vpre/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![Downloads](https://img.shields.io/packagist/dt/litgroup/enumerable.svg)](https://packagist.org/packages/litgroup/enumerable)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)][license]
[![Build Status](https://travis-ci.org/LitGroup/enumerable.php.svg?branch=master)](https://travis-ci.org/LitGroup/enumerable.php)

Example of usage.
-----------------

###  Define enumerable

For example let's create a `ColorEnum`:
    
1. We need to create custom class, which extends `Enumerable`.
   We recommend to use `Enum` suffix for all enumerable classes.
2. Enumerable class must be `final`.
3. For each variant of values we should create a static method, which
   will creates an instance of value. For this purpose your method
   must call `Enumerable::createEnum()` with index of enum.
   You can use some magic literals like `self::createEnum('red')`,
   but we strongly recommend to use constants: `self::createEnum(self::RED)`.
   Constants can be used in `switch-case` statements later.
    

**Complete Example**

```php

namespace App;

use LitGroup\Enumerable\Enumerable;

final class ColorEnum extends Enumerable
{
    const RED = 'red';
    const GREEN = 'green';
    const BLUE = 'blue';

    /**
     * @return self
     */
    public static function red()
    {
        return self::createEnum(self::RED);
    }

    /**
     * @return self
     */
    public static function green()
    {
        return self::createEnum(self::GREEN);
    }

    /**
     * @return self
     */
    public static function blue()
    {
        return self::createEnum(self::BLUE);
    }
}
```

### Use enumerable

Let's imagine, that we want to use some `AlertView` class, which abstracts
UI notifications and have some levels of importance. Importance can be
presented as `AlertLevelEnum` and have values: `info`, `warning`, `danger`.

**Declaration of AlertLevelEnum**

```php
namespace App;

use LitGroup\Enumerable\Enumerable;

final class AlertLevelEnum extends Enumerable
{
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';
 
    /**
     * @return AlertLevelEnum
     */
    public static function info()
    {
        return self::createEnum(self::INFO);
    }
    
    /**
     * @return AlertLevelEnum
     */
    public static function warning()
    {
        return self::createEnum(self::WARNING);
    }
    
    /**
     * @return AlertLevelEnum
     */
    public static function danger()
    {
        return self::createEnum(self::DANGER);
    }
}

```

**Implementation of AlertView**

```php
namespace App;

class AlertView
{
    /** @var string */
    private $message;
    
    /** @var ColorEnum*/
    private $level;
    
    
    public function __construct($message, AlertLevelEnum $level)
    {
        $this->message = $message;
        $this->level = $level;
    }
    
    /**
     * @return string HTML-Representation of alert.
     */
    public function renderHtml()
    {
        return sprintf(
            '<div style="background-color: %s">%s</div>',
            $this->getHtmlColor(),
            htmlentities($this->message)
        );
    }
    
    /**
     * @return string Code of color for CSS.
     */
    private function getHtmlColor()
    {
        // Resolve color by level of importance:
        switch ($this->level->index()) {
            case AlertLevelEnum::INFO:
                return '#d9edf7';
            
            case AlertLevelEnum::WARNING:
                return '#fcf8e3';
            
            case AlertLevelEnum::DANGER:
                return '#f2dede';
            
            default:
                return '#f5f5f5';
        }
    }
}
```

### Persistence and Serialization

`Enumerable` works as runtime-constant. Enumerable type cannot be serialized.
If you need to store representation of enumerable in a database or send
it via an API you can use index of enumerable as representation.

```php
$enum->getIndex();
```

To restore an instance of enumerable type by index from database or
from API-request you can use static method `getValue()` on the concrete
enum-class.

```php
$colorIndex = getFromDatabase(/* something */);

$enum = ColorEnum::getValue($colorIndex);
```

If you need to get all values of enumerable type, use static method
`getValues()` on the concrete enum-class.

```php
ColorEnum::getValues(); // => Returns array of ColorEnum with enum index as key
```

Run tests
---------

```bash
composer install
./tests.sh
```

LICENSE
-------

See [LICENSE][license] file.

[license]: https://raw.githubusercontent.com/LitGroup/enumerable.php/master/LICENSE