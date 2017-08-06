<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LitGroup\Enumerable;

use LitGroup\Equatable\Equatable;
use ReflectionClass;
use ReflectionMethod;
use LogicException;
use OutOfBoundsException;

/**
 * Class Enumerable
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
abstract class Enumerable
{
    /**
     * Cache of all available values of enumerables.
     *
     * @var array
     */
    private static $enums = [];

    /**
     * Marks when initialization in progress.
     *
     * @var bool
     */
    private static $isInInitializationState = false;

    /**
     * Current raw value  of the instance of the enumerable.
     *
     * @var int|string
     */
    private $rawValue;

    /**
     * Returns an instance of Enumerable by raw value.
     *
     * @param int|string $rawValue
     *
     * @return static
     */
    final public static function getValueOf($rawValue)
    {
        $values = static::getValues();
        if (!array_key_exists($rawValue, $values)) {
            throw new OutOfBoundsException(
                sprintf('Enum "%s" has no value with raw value "%s".', get_called_class(), $rawValue)
            );
        }

        return $values[$rawValue];
    }

    /**
     * Returns list of values which available for current enumerable.
     *
     * @return static[]
     */
    final public static function getValues()
    {
        if (self::isEnumNotInitialized(static::class)) {
            self::initializeEnum(static::class);
        }

        return self::$enums[static::class];
    }

    /**
     * Returns the raw value of the enumerable.
     *
     * @return mixed
     */
    final public function getRawValue()
    {
        return $this->rawValue;
    }

    public function equals(self $enum): bool
    {
        if (!$enum instanceof static) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot compare instance of "%s" with instance of "%s"',
                get_class($this),
                get_class($enum)
            ));
        }
        return $this === $enum;
    }

    /**
     * @param int|string $index
     *
     * @return static
     */
    final protected static function createEnum($index)
    {
        return self::$isInInitializationState ? new static($index) : static::getValueOf($index);
    }

    /*
     * Internal methods.
     * ********************************************************************* */

    /**
     * Initializes values of enumerable class.
     *
     * @param string $enumClass
     */
    private static function initializeEnum($enumClass)
    {
        self::$isInInitializationState = true;
        try {
            $classReflection = new ReflectionClass($enumClass);

            // Enumerable must be final:
            if (!$classReflection->isFinal()) {
                throw new LogicException(
                    sprintf('Enumerable class must be final, but "%s" is not final.', $enumClass)
                );
            }

            // Enumerable cannot be Serializable:
            if (is_subclass_of($enumClass, \Serializable::class)) {
                throw new LogicException(
                    sprintf(
                        'Enumerable cannot be serializable, but enum class "%s" implements "Serializable" interface.',
                        $enumClass
                    )
                );
            }

            $methods = $classReflection->getMethods(ReflectionMethod::IS_STATIC);

            self::$enums[$enumClass] = [];
            foreach ($methods as $method) {
                if (self::isServiceMethod($method)) {
                    continue;
                }

                /** @var Enumerable $value */
                $value = $method->invoke(null);

                if (!is_object($value) || get_class($value) !== $enumClass) {
                    throw new LogicException(sprintf(
                        '"%s:%s()" should return an instance of its class. But value of type "%s" returned.',
                        $enumClass,
                        $method,
                        is_object($value) ? get_class($value) : gettype($value)
                    ));
                }

                // Detect duplication of indexes:
                if (array_key_exists($value->getRawValue(), self::$enums[$enumClass])) {
                    throw new LogicException(
                        sprintf('Duplicate of index "%s" in enumerable "%s".', $value->getRawValue(), $enumClass)
                    );
                }
                self::$enums[$enumClass][$value->getRawValue()] = $value;
            }
        } finally {
            self::$isInInitializationState = false;
        }
    }

    /**
     * @param string $enumClass
     *
     * @return bool
     */
    private static function isEnumNotInitialized($enumClass)
    {
        return !array_key_exists($enumClass, self::$enums);
    }

    /**
     * Checks that given method is for the internal use.
     *
     * @param ReflectionMethod $method
     *
     * @return boolean
     */
    private static function isServiceMethod(ReflectionMethod $method)
    {
        return !$method->isPublic() || in_array($method->getShortName(), self::getServiceMethods());
    }

    /**
     * @return string[]
     */
    private static function getServiceMethods()
    {
        return [
            'getValueOf',
            'getValues',
        ];
    }

    /**
     * @param mixed $index
     *
     * @return bool
     */
    private static function isIndexTypeAllowed($index)
    {
        return is_int($index) || is_string($index);
    }

    final private function __construct($index)
    {
        if (!self::isIndexTypeAllowed($index)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Index of enumerable can be a "string" or an "int", but "%s" given.',
                    is_object($index) ? get_class($index) : gettype($index)
                )
            );
        }

        $this->rawValue = $index;
    }

    final private function __clone() {}
    final private function __sleep() {}
    final private function __wakeup() {}
}