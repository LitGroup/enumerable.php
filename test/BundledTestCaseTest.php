<?php
/*
 * This file is part of the "litgroup/enumerable" package.
 *
 * (c) Roman Shamritskiy <r.shamritskiy@litgroup.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\LitGroup\Enumerable;

use LitGroup\Enumerable\Test\EnumerableTestCase;

use Test\LitGroup\Enumerable\Fixtures\ColorEnum;

class BundledTestCaseTest extends EnumerableTestCase
{
    public function testEnumHasRawValueAssertion(): void
    {
        $this->assertEnumHasRawValue(ColorEnum::GREEN, ColorEnum::green());
    }

    public function testEnumHasRawValuesAssertion(): void
    {
        $this->assertEnumHasRawValues([
            ColorEnum::RED => ColorEnum::red(),
            ColorEnum::GREEN => ColorEnum::green(),
            ColorEnum::BLUE => ColorEnum::blue(),
        ]);
    }

    public function testEnumValuesCountAssertions(): void
    {
        $this->assertEnumValuesCount(3, ColorEnum::class);
    }
}
