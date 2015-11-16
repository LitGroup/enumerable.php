<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LitGroup\Enumerable\Tests;

use LitGroup\Enumerable\Enumerable;
use LitGroup\Enumerable\Tests\Fixtures\ColorEnum;
use LitGroup\Enumerable\Tests\Fixtures\ColorStrEnum;

class EnumerableTest extends \PHPUnit_Framework_TestCase
{
    public function testIndex()
    {
        $this->assertSame(ColorEnum::RED, ColorEnum::red()->getIndex());
        $this->assertSame(ColorEnum::GREEN, ColorEnum::green()->getIndex());
        $this->assertSame(ColorEnum::BLUE, ColorEnum::blue()->getIndex());
    }

    public function testGetValues()
    {
        $values = ColorEnum::getValues();
        $this->assertCount(3, $values);
        for ($i = 0; $i < 3; $i++) {
            $this->assertInstanceOf('LitGroup\Enumerable\Tests\Fixtures\ColorEnum', $values[$i]);
            $this->assertSame($i, $values[$i]->getIndex());
        }
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Enumerable class should be final, but "LitGroup\Enumerable\Enumerable" is not final.
     */
    public function testValuesForNonFinalEnum()
    {
        Enumerable::getValues();
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

    public function testSerialization()
    {
        $this->assertTrue(
            unserialize(serialize(ColorEnum::green())) == ColorEnum::green()
        );
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