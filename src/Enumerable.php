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
use DomainException;
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
    private static $initializationState = false;

    /**
     * Current index of the instance of the enumerable.
     *
     * @var mixed
     */
    private $index;


    /**
     * Returns an instance of Enumerable by index.
     *
     * @param mixed $index
     *
     * @return static
     *
     * @throws OutOfBoundsException
     */
    final public static function getValue($index)
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
     * @param mixed $index
     *
     * @return static
     */
    final protected static function createEnum($index)
    {
        if (self::$initializationState) {
            $enum = new static();
            $enum->index = $index;

            return $enum;
        }

        return static::getValue($index);
    }

    /*
     * Internal methods.
     * ********************************************************************* */

    /**
     * Initializes values of enumerable class.
     *
     * @param string $enumClass
     *
     * @throws DomainException
     */
    private static function initializeEnum($enumClass)
    {
        self::$initializationState = true;
        try {
            $classReflection = new \ReflectionClass($enumClass);

            // Enumerable must be final.
            if (!$classReflection->isFinal()) {
                throw new DomainException(
                    sprintf('Enumerable class must be final, but "%s" is not final.', $enumClass)
                );
            }

            // Enumerable cannot be Serializable.
            if (is_subclass_of($enumClass, \Serializable::class)) {
                throw new DomainException(
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
                self::$enums[$enumClass][$value->getIndex()] = $value;
            }
        } finally {
            self::$initializationState = false;
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
            'getValue',
            'getValues',
        ];
    }

    final protected function __construct() {}

    final private function __clone() {}
    final private function __sleep() {}
    final private function __wakeup() {}
}