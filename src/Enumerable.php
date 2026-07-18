<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace LitGroup\Enumerable;

use ReflectionClass;
use ReflectionMethod;
use LogicException;
use OutOfBoundsException;

/**
 * Class Enumerable
 *
 * @author Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 */
abstract class Enumerable
{
    private const SERVICE_METHOD_NAMES = ["getValueOf", "getValues", "cases", "from", "tryFrom"];

    /**
     * Instances of all initialized enum values.
     * 
     * @var array<class-string<Enumerable>, array<int|string, Enumerable>>
     */
    private static array $enums = [];

    /**
     * Marks when initialization in progress.
     *
     * @var bool
     */
    private static bool $initializationInProgress = false;

    /**
     * @param int|string $value The backed value of the case.
     */
    final private function __construct(public final readonly int|string $value) {}

    /**
     * Initializes a enum case with the given backed value.
     */
    #[\NoDiscard]
    final protected static function case(int|string $value): static
    {
        return self::$initializationInProgress ? new static($value) : static::from($value);
    }

    /**
     * Initializes a enum case with the given backed value.
     */
    #[\Deprecated("use case() instead", since: "0.9.0")]
    final protected static function createEnum(int|string $value): static
    {
        return self::case($value);
    }

    /**
     * Creates a enum from its backed value; results to fatal error for unknown value.
     *
     * Use `tryFrom()` to decode a enum from user input, it returns nullable
     * enum value instead of throwing error.
     */
    final public static function from(int|string $value): ?static
    {
        return self::tryFrom($value) ??
            throw new \ValueError(
                sprintf(
                    "%s is not a valid backing value for enum %s",
                    (string) $value,
                    static::class,
                ),
            );
    }

    /**
     * Creates a enum from its backed value; returns null for unknown value.
     */
    final public static function tryFrom(int|string $value): ?static
    {
        self::initializeIfNotYet();

        return self::$enums[static::class][$value] ?? null;
    }

    /**
     * Returns an instance of enum by backed value.
     */
    #[\Deprecated("use from() or tryFrom() instead", since: "0.9.0")]
    final public static function getValueOf(int|string $value): static
    {
        return self::tryFrom($value) ??
            throw new OutOfBoundsException(
                sprintf('Enum "%s" has no value with backed value "%s".', static::class, $value),
            );
    }

    /**
     * Returns map of raw representation to enum values which available for current enumerable.
     *
     * @return array<string|int, static>
     */
    #[\Deprecated("use cases() instead", since: "0.9.0")]
    final public static function getValues(): array
    {
        self::initializeIfNotYet();

        return self::$enums[static::class];
    }

    /**
     * Returns list of cases of the enum.
     *
     * @return static[]
     */
    final public static function cases(): array
    {
        self::initializeIfNotYet();

        return array_values(self::$enums[static::class]);
    }

    /**
     * Returns the backed value of the enum.
     */
    #[\Deprecated("use `value` property instead", since: "0.9.0")]
    final public function getRawValue(): int|string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    private static function initializeIfNotYet(): void
    {
        if (array_key_exists(static::class, self::$enums)) {
            // Current enum type is initialized already.
            // Nothing to do.
            return;
        }

        self::initializeEnum(static::class);
    }

    /**
     * Initializes values of enumerable class.
     *
     * @param class-string<Enumerable> $class
     */
    private static function initializeEnum(string $class): void
    {
        try {
            self::$initializationInProgress = true;

            $classReflection = new ReflectionClass($class);

            // Enumerable must be final:
            if (!$classReflection->isFinal()) {
                throw new LogicException("Enum class must be final, but $class is not.");
            }
            // Enumerable cannot be Serializable:
            if (is_subclass_of($class, \Serializable::class)) {
                throw new LogicException(
                    "Enum cannot be serialized, but $class implements Serializable interface.",
                );
            }
            // Enum types should not implement \Stringable, it is deprecated.
            if (is_subclass_of($class, \Stringable::class)) {
                trigger_error("Implementation of \Stringable is deprecated for enumerable types.", E_USER_DEPRECATED);
            }

            // Looking for case factory methods:
            $staticMethods = $classReflection->getMethods(ReflectionMethod::IS_STATIC);

            /** @var array<int|string, static> $cases */
            $cases = [];

            foreach ($staticMethods as $method) {
                if (self::isServiceMethod($method)) {
                    continue;
                }

                $instance = $method->invoke(null);
                if (!$instance instanceof static) {
                    throw new LogicException(
                        sprintf(
                            '"%s:%s()" should return an instance of its class. But value of type "%s" returned.',
                            $class,
                            $method,
                            is_object($instance) ? get_class($instance) : gettype($instance),
                        ),
                    );
                }

                // Detect duplication of backed value:
                if (array_key_exists($instance->value, $cases)) {
                    throw new LogicException(
                        "Duplicate of backed value `{$instance->value}` in enum $class.",
                    );
                }
                $cases[$instance->value] = $instance;
            }

            // Commit initialized cases:
            self::$enums[$class] = $cases;
        } finally {
            self::$initializationInProgress = false;
        }
    }

    /**
     * @param string $enumClass
     *
     * @return bool
     */
    #[\Deprecated]
    private static function isEnumNotInitialized($enumClass): bool
    {
        return !array_key_exists($enumClass, self::$enums);
    }

    /**
     * Checks that given method is one of service method provided by Enumerable.
     */
    private static function isServiceMethod(ReflectionMethod $method): bool
    {
        return !$method->isPublic() ||
            in_array($method->getShortName(), self::SERVICE_METHOD_NAMES);
    }

    final public function __clone(): never
    {
        throw new \BadMethodCallException("Cloning is restricted for enumerable types");
    }

    final public function __serialize(): never
    {
        throw new \BadMethodCallException("Serialization is restricted for enumerable types");
    }

    final public function __unserialize(array $data): never
    {
        throw new \BadMethodCallException("Serialization is restricted for enumerable types");
    }
}
