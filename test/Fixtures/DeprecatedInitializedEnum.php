<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\LitGroup\Enumerable\Fixtures;

use LitGroup\Enumerable\Enumerable;

final class DeprecatedInitializedEnum extends Enumerable
{
    public static function some(): self
    {
        return self::createEnum("some");
    }
}
