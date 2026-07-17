<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\LitGroup\Enumerable;

use LitGroup\Enumerable\Test\EnumerableTestCase;
use Test\LitGroup\Enumerable\Fixtures\AnotherColorEnum;
use Test\LitGroup\Enumerable\Fixtures\ColorEnum;
use Test\LitGroup\Enumerable\Fixtures\DuplicateIndexEnum;
use Test\LitGroup\Enumerable\Fixtures\FloatIndexedEnum;
use Test\LitGroup\Enumerable\Fixtures\InvalidReturnTypeEnum;
use Test\LitGroup\Enumerable\Fixtures\InvalidScalarReturnTypeEnum;
use Test\LitGroup\Enumerable\Fixtures\SerializableEnum;
use Test\LitGroup\Enumerable\Fixtures\NonFinalEnum;

class EnumerableTest extends EnumerableTestCase
{
    public function testIndex(): void
    {
        $this->assertEnumHasRawValues([
            ColorEnum::RED => ColorEnum::red(),
            ColorEnum::GREEN => ColorEnum::green(),
            ColorEnum::BLUE => ColorEnum::blue(),
        ]);
    }

    public function testEquality(): void
    {
        $this->assertEquals(ColorEnum::red(), ColorEnum::red());
        $this->assertNotEquals(ColorEnum::red(), ColorEnum::green());

        $this->assertTrue(ColorEnum::red()->equals(ColorEnum::red()));
        $this->assertFalse(ColorEnum::red()->equals(ColorEnum::blue()));
    }

    public function testExceptionOnEqualityCheckOfDifferentTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ColorEnum::red()->equals(AnotherColorEnum::red());
    }

    public function testIdentity(): void
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::red());
        $this->assertNotSame(ColorEnum::red(), ColorEnum::green());
    }

    public function testSwitchStatement(): void
    {
        switch (ColorEnum::green()) {
            case ColorEnum::green():
                $this->assertTrue(true); // To avoid "no assertion" notification of PHPUnit
                break;
            default:
                $this->fail("GREEN case had to be called.");
        }
    }

    public function testGetValueOf(): void
    {
        $this->assertSame(
            ColorEnum::red(),
            ColorEnum::getValueOf(ColorEnum::RED),
        );
        $this->assertSame(
            ColorEnum::green(),
            ColorEnum::getValueOf(ColorEnum::GREEN),
        );
        $this->assertSame(
            ColorEnum::blue(),
            ColorEnum::getValueOf(ColorEnum::BLUE),
        );
    }

    public function testGetValueForNonExistentIndex(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        ColorEnum::getValueOf("incorrect_index");
    }

    public function testGetValues(): void
    {
        $this->assertSame(
            [
                ColorEnum::RED => ColorEnum::red(),
                ColorEnum::GREEN => ColorEnum::green(),
                ColorEnum::BLUE => ColorEnum::blue(),
            ],
            ColorEnum::getValues(),
        );
    }

    public function testCases(): void
    {
        $this->assertSame(
            [ColorEnum::red(), ColorEnum::green(), ColorEnum::blue()],
            ColorEnum::cases(),
        );
    }

    public function testValuesForNonFinalEnum(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Enumerable class must be final, but "Test\LitGroup\Enumerable\Fixtures\NonFinalEnum" is not final',
        );
        NonFinalEnum::getValues();
    }

    public function testEnumCannotBeSerializable(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(
            'Enumerable cannot be serializable, but enum class "Test\LitGroup\Enumerable\Fixtures\SerializableEnum" implements "Serializable" interface',
        );
        SerializableEnum::getValues();
    }

    public function testInitializationExceptionOnDuplicateIndex(): void
    {
        $this->expectException(\LogicException::class);
        DuplicateIndexEnum::some();
    }

    public function testOnlyStringOrIntCanBeUsedForIndex(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FloatIndexedEnum::one();
    }

    public function testShouldThrowAnExceptionIfEnumMethodReturnsInstanceOfDifferentClass(): void
    {
        $this->expectException(\LogicException::class);
        InvalidReturnTypeEnum::getValues();
    }

    public function testExceptionWhenEnumFactoryMethodReturnsScalarValue(): void
    {
        $this->expectException(\LogicException::class);
        InvalidScalarReturnTypeEnum::getValues();
    }

    public function testClone(): void
    {
        $red = ColorEnum::red();
        $this->expectException(\BadMethodCallException::class);
        $otherRed = clone $red;
    }

    public function testSerialize(): void
    {
        $red = ColorEnum::red();
        $this->expectException(\BadMethodCallException::class);
        serialize($red);
    }
}
