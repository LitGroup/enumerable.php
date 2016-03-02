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

use Tests\LitGroup\Enumerable\Fixtures\ColorEnum;
use Tests\LitGroup\Enumerable\Fixtures\SerializableEnum;
use Tests\LitGroup\Enumerable\Fixtures\NonFinalEnum;

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

    public function testGetValue()
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::getValue(ColorEnum::RED));
        $this->assertSame(ColorEnum::green(), ColorEnum::getValue(ColorEnum::GREEN));
        $this->assertSame(ColorEnum::blue(), ColorEnum::getValue(ColorEnum::BLUE));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetValueForNonExistentIndex()
    {
        ColorEnum::getValue('incorrect_index');
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
     * @expectedException \DomainException
     * @expectedExceptionMessage Enumerable class must be final, but "Tests\LitGroup\Enumerable\Fixtures\NonFinalEnum" is not final.
     */
    public function testValuesForNonFinalEnum()
    {
        NonFinalEnum::getValues();
    }

    /**
     * @expectedException \DomainException
     * @expectedExceptionMessage Enumerable cannot be serializable, but enum class "Tests\LitGroup\Enumerable\Fixtures\SerializableEnum" implements "Serializable" interface.
     */
    public function testEnumCannotBeSerializable()
    {
        SerializableEnum::getValues();
    }
}