<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\LitGroup\Enumerable\Fixtures;

use LitGroup\Enumerable\Enumerable;

final class ColorEnum extends Enumerable
{
    const RED = 0;
    const GREEN = 1;
    const BLUE = 2;

    /**
     * @return self
     */
    public static function red()
    {
        return self::createEnum(self::RED);
    }

    /**
     * @return self
     */
    public static function green()
    {
        return self::createEnum(self::GREEN);
    }

    /**
     * @return self
     */
    public static function blue()
    {
        return self::createEnum(self::BLUE);
    }
}