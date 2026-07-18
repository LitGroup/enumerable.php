<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\LitGroup\Enumerable\Fixtures;

use LitGroup\Enumerable\Enumerable;

class NonFinalEnum extends Enumerable
{
    const RED = "RED";
    const GREEN = "GREEN";
    const BLUE = "BLUE";

    public static function red(): self
    {
        return self::createEnum(self::RED);
    }

    public static function green(): self
    {
        return self::createEnum(self::GREEN);
    }

    public static function blue(): self
    {
        return self::createEnum(self::BLUE);
    }
}
