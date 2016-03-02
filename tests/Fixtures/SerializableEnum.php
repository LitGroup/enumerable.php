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

final class SerializableEnum extends Enumerable implements \Serializable
{
    const RED = 'RED';
    const GREEN = 'GREEN';
    const BLUE = 'BLUE';

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

    public function serialize()
    {
        // Nothing to do.
    }

    public function unserialize($serialized)
    {
        // Nothing to do.
    }
}