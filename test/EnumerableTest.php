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

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetValueForNonExistentIndex()
    {
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

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Enumerable class must be final, but "Test\LitGroup\Enumerable\Fixtures\NonFinalEnum" is not final.
     */
    public function testValuesForNonFinalEnum()
    {
        NonFinalEnum::getValues();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Enumerable cannot be serializable, but enum class "Test\LitGroup\Enumerable\Fixtures\SerializableEnum" implements "Serializable" interface.
     */
    public function testEnumCannotBeSerializable()
    {
        SerializableEnum::getValues();
    }

    /**
     * @expectedException \LogicException
     */
    public function testInitializationExceptionOnDuplicateIndex()
    {
        DuplicateIndexEnum::some();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyStringOrIntCanBeUsedForIndex()
    {
        FloatIndexedEnum::one();
    }

    /**
     * @expectedException \LogicException
     */
    public function testShouldThrowAnExceptionIfEnumMethodReturnsInstanceOfDifferentClass()
    {
        InvalidReturnTypeEnum::getValues();
    }

    /**
     * @expectedException \LogicException
     */
    public function testExceptionWhenEnumFactoryMethodReturnsScalarValue()
    {
        InvalidScalarReturnTypeEnum::getValues();
    }
}