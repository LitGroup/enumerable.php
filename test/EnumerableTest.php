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

use Test\LitGroup\Enumerable\Fixtures\ColorEnum;
use Test\LitGroup\Enumerable\Fixtures\DuplicateIndexEnum;
use Test\LitGroup\Enumerable\Fixtures\FloatIndexedEnum;
use Test\LitGroup\Enumerable\Fixtures\SerializableEnum;
use Test\LitGroup\Enumerable\Fixtures\NonFinalEnum;

class EnumerableTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $this->assertSame(ColorEnum::RED, ColorEnum::red()->getIndex());
        $this->assertSame(ColorEnum::GREEN, ColorEnum::green()->getIndex());
        $this->assertSame(ColorEnum::BLUE, ColorEnum::blue()->getIndex());
    }

    public function testEquality()
    {
        $a = ColorEnum::red();
        $b = ColorEnum::red();
        $c = ColorEnum::green();

        $this->assertEquals($a, $b);
        $this->assertNotEquals($a, $c);
    }

    public function testIdentity()
    {
        $a = ColorEnum::red();
        $b = ColorEnum::red();
        $c = ColorEnum::green();

        $this->assertSame($a, $b);
        $this->assertNotSame($a, $c);
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
}