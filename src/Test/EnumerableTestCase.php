<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LitGroup\Enumerable\Test;

use LitGroup\Enumerable\Enumerable;
use PHPUnit\Framework\TestCase;

/**
 * Abstract test case for enumerable classes.
 *
 * @author Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 *
 * @codeCoverageIgnore
 * @deprecated Since 0.9.0, will be removed in 0.10.0.
 */
abstract class EnumerableTestCase extends TestCase
{
    /**
     * Asserts rawValues for enumerable values.
     *
     * @param array $payload Map of rawValue => $value
     *
     * @example
     *     $this->assertEnumIndexes([
     *         'red'   => ColorEnum::red(),
     *         'green' => ColorEnum::green(),
     *         'blue'  => ColorEnum::blue(),
     *     ]);
     */
    #[\Deprecated(since: "0.9.0")]
    public static function assertEnumHasRawValues(array $payload): void
    {
        if (count($payload) === 0) {
            throw new \InvalidArgumentException('$payload should not be empty');
        }

        $enumClass = get_class(reset($payload));

        self::assertEnumValuesCount(count($payload), $enumClass);
        foreach ($payload as $expectedIndex => $value) {
            self::assertInstanceOf($enumClass, $value);
            self::assertEnumHasRawValue($expectedIndex, $value);
        }
    }

    /**
     * Asserts that Enumerable has the same raw value as expected.
     *
     * @param mixed $expected Expected raw value.
     * @param Enumerable $enum Enumerable value.
     */
    #[\Deprecated(since: "0.9.0")]
    public static function assertEnumHasRawValue(
        $expected,
        Enumerable $enum,
    ): void {
        self::assertSame($expected, $enum->value);
    }

    /**
     * Asserts that enumerable class contains expected amount of values.
     *
     * @param int $expectedAmountOfValues
     * @param string $enumClass Name of enumerable class.
     */
    #[\Deprecated(since: "0.9.0")]
    public static function assertEnumValuesCount(
        $expectedAmountOfValues,
        $enumClass,
    ): void {
        $expectedAmountOfValues = (int) $expectedAmountOfValues;

        if (!is_subclass_of($enumClass, Enumerable::class)) {
            throw new \InvalidArgumentException(
                '$enumClass must be a name of enumerable class.',
            );
        }

        $actualAmountOfValues = count(
            call_user_func([$enumClass, "getValues"]),
        );
        self::assertSame(
            $expectedAmountOfValues,
            $actualAmountOfValues,
            sprintf(
                'Enumerable class "%s" contains unexpected amount of values (%d instead of %d)',
                $enumClass,
                $actualAmountOfValues,
                $expectedAmountOfValues,
            ),
        );
    }
}
