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

final class DeprecatedStringableEnum extends Enumerable implements \Stringable
{
    public static function a(): self
    {
        return self::case("A");
    }

    public static function b(): self
    {
        return self::case("B");
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
