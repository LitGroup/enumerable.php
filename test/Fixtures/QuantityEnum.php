<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\LitGroup\Enumerable\Fixtures;

use LitGroup\Enumerable\Enumerable;

final class QuantityEnum extends Enumerable
{
    const SINGLE = 1;
    const DOUBLE = 2;
    const TRIPLE = 3;

    public static function single(): self
    {
        return self::case(self::SINGLE);
    }

    public static function double(): self
    {
        return self::case(self::DOUBLE);
    }

    public static function triple(): self
    {
        return self::case(self::TRIPLE);
    }
}
