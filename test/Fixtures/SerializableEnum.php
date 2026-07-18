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

final class SerializableEnum extends Enumerable implements \Serializable
{
    const RED = "RED";
    const GREEN = "GREEN";
    const BLUE = "BLUE";

    public static function red(): self
    {
        return self::case(self::RED);
    }

    public static function green(): self
    {
        return self::case(self::GREEN);
    }

    public static function blue(): self
    {
        return self::case(self::BLUE);
    }

    public function serialize()
    {
        // Nothing to do.
    }

    public function unserialize($serialized)
    {
        // Nothing to do.
    }
}
