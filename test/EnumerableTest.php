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
    public function testIndex()
    {
        $this->assertEnumHasRawValues([
            ColorEnum::RED => ColorEnum::red(),
            ColorEnum::GREEN => ColorEnum::green(),
            ColorEnum::BLUE => ColorEnum::blue(),
        ]);
    }

    public function testEquality()
    {
        $this->assertEquals(ColorEnum::red(), ColorEnum::red());
        $this->assertNotEquals(ColorEnum::red(), ColorEnum::green());

        $this->assertTrue(ColorEnum::red()->equals(ColorEnum::red()));
        $this->assertFalse(ColorEnum::red()->equals(ColorEnum::blue()));
    }

    public function testExceptionOnEqualityCheckOfDifferentTypes()
    {
        $this->expectException(\InvalidArgumentException::class);
        ColorEnum::red()->equals(AnotherColorEnum::red());
    }

    public function testIdentity()
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::red());
        $this->assertNotSame(ColorEnum::red(), ColorEnum::green());
    }

    public function testSwitchStatement()
    {
        switch (ColorEnum::green()) {
            case ColorEnum::green():
                break;
            default:
                $this->fail('GREEN case had to be called.');
        }
    }

    public function testGetValueOf()
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::getValueOf(ColorEnum::RED));
        $this->assertSame(ColorEnum::green(), ColorEnum::getValueOf(ColorEnum::GREEN));
        $this->assertSame(ColorEnum::blue(), ColorEnum::getValueOf(ColorEnum::BLUE));
    }

    public function testGetValueForNonExistentIndex()
    {
        $this->expectException(\OutOfBoundsException::class);
        ColorEnum::getValueOf('incorrect_index');
    }

    public function testGetValues()
    {
        $this->assertSame(
            [
                ColorEnum::RED => ColorEnum::red(),
                ColorEnum::GREEN => ColorEnum::green(),
                ColorEnum::BLUE => ColorEnum::blue(),
            ],
            ColorEnum::getValues()
        );
    }

    public function testValuesForNonFinalEnum()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Enumerable class must be final, but "Test\LitGroup\Enumerable\Fixtures\NonFinalEnum" is not final');
        NonFinalEnum::getValues();
    }

    public function testEnumCannotBeSerializable()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Enumerable cannot be serializable, but enum class "Test\LitGroup\Enumerable\Fixtures\SerializableEnum" implements "Serializable" interface');
        SerializableEnum::getValues();
    }

    public function testInitializationExceptionOnDuplicateIndex()
    {
        $this->expectException(\LogicException::class);
        DuplicateIndexEnum::some();
    }

    public function testOnlyStringOrIntCanBeUsedForIndex()
    {
        $this->expectException(\InvalidArgumentException::class);
        FloatIndexedEnum::one();
    }

    public function testShouldThrowAnExceptionIfEnumMethodReturnsInstanceOfDifferentClass()
    {
        $this->expectException(\LogicException::class);
        InvalidReturnTypeEnum::getValues();
    }

    public function testExceptionWhenEnumFactoryMethodReturnsScalarValue()
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