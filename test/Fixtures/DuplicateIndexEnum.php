<?php
/**
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <roman@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\LitGroup\Enumerable\Fixtures;

use LitGroup\Enumerable\Enumerable;

final class DuplicateIndexEnum extends Enumerable
{
    const INDEX = 'some_index';

    /**
     * @return self
     */
    public static function some()
    {
        return self::createEnum(self::INDEX);
    }

    /**
     * @return self
     */
    public static function another()
    {
        return self::createEnum(self::INDEX);
    }
}