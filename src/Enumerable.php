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
     * Cache of all available values of Enum.
     *
     * @var array
     */
    private static $values = [];

    /**
     * Current index of the instance of Enumerable.
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
        if (!array_key_exists($index, static::getValues())) {
            throw new OutOfBoundsException(
                sprintf('Enum "%s" has no value indexed by "%s".', get_called_class(), $index)
            );
        }

        return static::getValues()[$index];
    }

    /**
     * Returns list of values which available for current enumerable.
     *
     * @return static[]
     */
    final public static function getValues()
    {
        $enumClass = get_called_class();

        if (!array_key_exists($enumClass, self::$values)) {
            self::$values[$enumClass] = self::initializeValuesForClass($enumClass);
        }

        return self::$values[$enumClass];
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
     * Protected constructor for using internally in child classes.
     *
     * @param mixed $index
     */
    final protected function __construct($index)
    {
        $this->index = $index;
    }

    /**
     * Initializes values of enumerable class.
     *
     * @param string $enumClass
     *
     * @return Enumerable[]
     *
     * @throws LogicException
     */
    private static function initializeValuesForClass($enumClass)
    {
        $classReflection = new \ReflectionClass($enumClass);

        if (!$classReflection->isFinal()) {
            throw new LogicException(
                sprintf('Enumerable class should be final, but "%s" is not final.', $enumClass)
            );
        }

        $methods = $classReflection->getMethods(ReflectionMethod::IS_STATIC);

        $values = [];
        foreach ($methods as $method) {
            if (self::isServiceMethod($method)) {
                continue;
            }

            /** @var Enumerable $value */
            $value = $method->invoke(null);
            $values[$value->getIndex()] = $value;
        }

        return $values;
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
        return !$method->isPublic() || in_array($method->getShortName(), ['getValue', 'getValues']);
    }
}