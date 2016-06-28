<?php
declare(strict_types = 1);

namespace Tests\LitGroup\Enumerable\Fixtures;

use LitGroup\Enumerable\Enumerable;

final class FloatIndexedEnum extends Enumerable
{
    public static function one()
    {
        return self::createEnum(1.0);
    }
}