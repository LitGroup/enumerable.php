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

final class InvalidReturnTypeEnum extends Enumerable
{
    /**
     * Returns an instance of another enumerable class.
     */
    public static function someValue(): Enumerable
    {
        return ColorEnum::red();
    }
}
