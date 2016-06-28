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
     * Current index of the instance of the enumerable.
     *
     * @var int|string
     */
    private $index;


    /**
     * @deprecated 0.4.0 Use getValueOf() instead.
     *
     * @param int|string $index
     *
     * @return static
     */
    final public static function getValue($index)
    {
        trigger_error(
            sprintf('Method %s::getValue() is deprecated. Use getValueOf() instead.', get_called_class()),
            \E_USER_DEPRECATED
        );

        return self::getValueOf($index);
    }

    /**
     * Returns an instance of Enumerable by index.
     *
     * @param int|string $index
     *
     * @return static
     */
    final public static function getValueOf($index)
    {
        $values = static::getValues();
        if (!array_key_exists($index, $values)) {
            throw new OutOfBoundsException(
                sprintf('Enum "%s" has no value indexed by "%s".', get_called_class(), $index)
            );
        }

        return $values[$index];
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
     * Returns the index of the enumerable.
     *
     * @return mixed
     */
    final public function getIndex()
    {
        return $this->index;
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
            $classReflection = new \ReflectionClass($enumClass);

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

                // Detect duplication of indexes:
                if (array_key_exists($value->getIndex(), self::$enums[$enumClass])) {
                    throw new LogicException(
                        sprintf('Duplicate of index "%s" in enumerable "%s".', $value->getIndex(), $enumClass)
                    );
                }
                self::$enums[$enumClass][$value->getIndex()] = $value;
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
            'getValue',   // TODO: Remove, when method getValue() will be removed.
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

        $this->index = $index;
    }

    final private function __clone() {}
    final private function __sleep() {}
    final private function __wakeup() {}
}