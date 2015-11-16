<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LitGroup\Enumerable\Tests\Fixtures;

use LitGroup\Enumerable\Enumerable;

final class ColorStrEnum extends Enumerable
{
    const RED = 'red';
    const GREEN = 'green';
    const BLUE = 'blue';

    /**
     * @return self
     */
    public static function red()
    {
        return new self(self::RED);
    }
    /**
     * @return self
     */
    public static function green()
    {
        return new self(self::GREEN);
    }
    /**
     * @return self
     */
    public static function blue()
    {
        return new self(self::BLUE);
    }
}