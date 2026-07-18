<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <r.shamritskiy@litgroup.ru>
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
 * @author Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 */
abstract class Enumerable
{
    private const SERVICE_METHOD_NAMES = [
        "getValueOf",
        "getValues",
        "cases",
        "from",
        "tryFrom",
    ];

    /**
     * Cache of all available values of enumerables.
     *
     * @var array<class-string<Enumerable>, array<int|string, Enumerable>>
     */
    private static array $enums = [];

    /**
     * Marks when initialization in progress.
     *
     * @var bool
     */
    private static bool $isInInitializationState = false;

    /**
     * @param int|string $value The backed value of the case.
     */
    final private function __construct(public readonly int|string $value) {}

    /**
     * Initializes a enum case with the given backed value.
     */
    #[\NoDiscard]
    final protected static function case(int|string $value): static
    {
        return self::$isInInitializationState
            ? new static($value)
            : static::getValueOf($value);
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
        if (self::isEnumNotInitialized(static::class)) {
            self::initializeEnum(static::class);
        }

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
                sprintf(
                    'Enum "%s" has no value with raw value "%s".',
                    get_called_class(),
                    $value,
                ),
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
        if (self::isEnumNotInitialized(static::class)) {
            self::initializeEnum(static::class);
        }

        return self::$enums[static::class];
    }

    /**
     * Returns list of cases of the enum.
     *
     * @return static[]
     */
    final public static function cases(): array
    {
        if (self::isEnumNotInitialized(static::class)) {
            self::initializeEnum(static::class);
        }

        return array_values(self::$enums[static::class]);
    }

    /**
     * Returns the backed value of the enum.
     */
    #[\Deprecated("use ->value property instead", since: "0.9.0")]
    final public function getRawValue(): int|string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    /**
     * Initializes values of enumerable class.
     *
     * @param class-string<Enumerable> $enumClass
     */
    private static function initializeEnum($enumClass): void
    {
        self::$isInInitializationState = true;
        try {
            $classReflection = new ReflectionClass($enumClass);

            // Enumerable must be final:
            if (!$classReflection->isFinal()) {
                throw new LogicException(
                    sprintf(
                        'Enumerable class must be final, but "%s" is not final.',
                        $enumClass,
                    ),
                );
            }

            // Enumerable cannot be Serializable:
            if (is_subclass_of($enumClass, \Serializable::class)) {
                throw new LogicException(
                    sprintf(
                        'Enumerable cannot be serializable, but enum class "%s" implements "Serializable" interface.',
                        $enumClass,
                    ),
                );
            }

            $methods = $classReflection->getMethods(
                ReflectionMethod::IS_STATIC,
            );

            self::$enums[$enumClass] = [];
            foreach ($methods as $method) {
                if (self::isServiceMethod($method)) {
                    continue;
                }

                /** @var Enumerable $value */
                $value = $method->invoke(null);

                if (!is_object($value) || get_class($value) !== $enumClass) {
                    throw new LogicException(
                        sprintf(
                            '"%s:%s()" should return an instance of its class. But value of type "%s" returned.',
                            $enumClass,
                            $method,
                            is_object($value)
                                ? get_class($value)
                                : gettype($value),
                        ),
                    );
                }

                // Detect duplication of indexes:
                if (
                    array_key_exists(
                        $value->getRawValue(),
                        self::$enums[$enumClass],
                    )
                ) {
                    throw new LogicException(
                        sprintf(
                            'Duplicate of index "%s" in enumerable "%s".',
                            $value->getRawValue(),
                            $enumClass,
                        ),
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

    final public function __clone()
    {
        throw new \BadMethodCallException(
            "Cloning is restricted for enumerable types",
        );
    }

    final public function __sleep()
    {
        throw new \BadMethodCallException(
            "Serialization is restricted for enumerable types",
        );
    }

    final public function __wakeup()
    {
        throw new \BadMethodCallException(
            "Serialization is restricted for enumerable types",
        );
    }
}
