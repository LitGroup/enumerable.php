<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\LitGroup\Enumerable;

use LitGroup\Enumerable\Enumerable;
use Tests\LitGroup\Enumerable\Fixtures\ColorEnum;
use Tests\LitGroup\Enumerable\Fixtures\ColorEnumSerializable;
use Tests\LitGroup\Enumerable\Fixtures\ColorStrEnum;

class EnumerableTest extends \PHPUnit_Framework_TestCase
{
    public function testGetValues()
    {
        $values = ColorEnum::getValues();
        $this->assertCount(3, $values);
        for ($i = 0; $i < 3; $i++) {
            $this->assertInstanceOf(ColorEnum::class, $values[$i]);
            $this->assertSame($i, $values[$i]->getIndex());
        }
    }

    public function testIndex()
    {
        $this->assertSame(ColorEnum::RED, ColorEnum::red()->getIndex());
        $this->assertSame(ColorEnum::GREEN, ColorEnum::green()->getIndex());
        $this->assertSame(ColorEnum::BLUE, ColorEnum::blue()->getIndex());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Enumerable class must be final, but "LitGroup\Enumerable\Enumerable" is not final.
     */
    public function testValuesForNonFinalEnum()
    {
        Enumerable::getValues();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Enumerable cannot be serializable, but enum class "Tests\LitGroup\Enumerable\Fixtures\ColorEnumSerializable" implements "Serializable" interface.
     */
    public function testEnumCannotBeSerializable()
    {
        ColorEnumSerializable::getValues();
    }

    public function provideGetValueTests()
    {
        return [
            [ColorEnum::RED, ColorEnum::red()],
            [ColorEnum::GREEN, ColorEnum::green()],
            [ColorEnum::BLUE, ColorEnum::blue()],
        ];
    }

    /**
     * @dataProvider provideGetValueTests
     */
    public function testGetValue($index, ColorEnum $expected)
    {
        $this->assertEquals($expected, ColorEnum::getValue($index));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetValue_OutOfBoundsException()
    {
        ColorEnum::getValue(100);
    }

    public function testEquivalency()
    {
        $this->assertEquals(ColorEnum::blue(), ColorEnum::blue());
        $this->assertNotEquals(ColorEnum::blue(), ColorEnum::red());
    }

    public function testIdentity()
    {
        $this->assertSame(ColorEnum::blue(), ColorEnum::blue());
        $this->assertNotSame(ColorEnum::blue(), ColorEnum::red());
    }

    public function testEnumerableForTextIndexes()
    {
        $red = ColorStrEnum::red();
        $green = ColorStrEnum::green();
        $blue = ColorStrEnum::blue();

        $this->assertEquals(ColorStrEnum::red(), $red);
        $this->assertEquals(ColorStrEnum::RED, $red->getIndex());

        $this->assertEquals(ColorStrEnum::green(), $green);
        $this->assertEquals(ColorStrEnum::GREEN, $green->getIndex());

        $this->assertEquals(ColorStrEnum::blue(), $blue);
        $this->assertEquals(ColorStrEnum::BLUE, $blue->getIndex());
    }
}