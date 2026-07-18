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

namespace Test\LitGroup\Enumerable;

use PHPUnit\Framework\TestCase;
use Test\LitGroup\Enumerable\Fixtures\{
    AnotherColorEnum,
    ColorEnum,
    DeprecatedStringableEnum,
    InvalidEnumDuplicatingBackedValues,
    InvalidReturnTypeEnum,
    InvalidScalarReturnTypeEnum,
    InvalidSerializableEnum,
    InvalidNonFinalEnum,
    QuantityEnum,
};

class EnumerableTest extends TestCase
{
    public function testReadingStringBackedValue(): void
    {
        $this->assertSame(ColorEnum::RED, ColorEnum::red()->value);
        $this->assertSame(ColorEnum::GREEN, ColorEnum::green()->value);
        $this->assertSame(ColorEnum::BLUE, ColorEnum::blue()->value);
    }

    public function testReadingIntBackedValue(): void
    {
        $this->assertSame(QuantityEnum::SINGLE, QuantityEnum::single()->value);
        $this->assertSame(QuantityEnum::DOUBLE, QuantityEnum::double()->value);
        $this->assertSame(QuantityEnum::TRIPLE, QuantityEnum::triple()->value);
    }

    public function testEquality(): void
    {
        // Test equality with `==` operator.
        $this->assertEquals(ColorEnum::red(), ColorEnum::red());
        $this->assertNotEquals(ColorEnum::red(), ColorEnum::green());
        $this->assertNotEquals(
            ColorEnum::red(),
            AnotherColorEnum::red(),
            "Same backed value but different enum type.",
        );

        // Test equality with equals() method.
        $this->assertTrue(ColorEnum::red()->equals(ColorEnum::red()));
        $this->assertFalse(ColorEnum::red()->equals(ColorEnum::blue()));
        $this->assertFalse(
            ColorEnum::red()->equals(AnotherColorEnum::red()),
            "Same backed value but different enum type.",
        );
    }

    public function testIdentity(): void
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::red());
        $this->assertNotSame(ColorEnum::red(), ColorEnum::green());
    }

    public function testSwitchStatementBehavior(): void
    {
        switch (ColorEnum::green()) {
            case ColorEnum::red():
                $this->fail("GREEN case had to be called.");
            case ColorEnum::green():
                $this->assertTrue(true); // To avoid "no assertion" notification of PHPUnit
                break;
            default:
                $this->fail("GREEN case had to be called.");
        }
    }

    public function testMatchExpressionBehavior(): void
    {
        $value = match (ColorEnum::green()) {
            ColorEnum::red() => "red",
            ColorEnum::green() => "green", // expected arm
            ColorEnum::blue() => "blue",
        };

        $this->assertEquals("green", $value);
    }

    public function testDecodingEnumFromBackedValueOrNull(): void
    {
        // Call tryFrom() first to check enum internal initialization is triggered byt he method
        $this->assertNotNull(ColorEnum::tryFrom("RED"));
        $this->assertSame(ColorEnum::red(), ColorEnum::tryFrom("RED"));
        $this->assertNotNull(ColorEnum::tryFrom("GREEN"));
        $this->assertSame(ColorEnum::green(), ColorEnum::tryFrom("GREEN"));

        $this->assertSame(QuantityEnum::single(), QuantityEnum::tryFrom(1));
        $this->assertSame(QuantityEnum::double(), QuantityEnum::tryFrom(2));

        $this->assertNull(ColorEnum::tryFrom("unknown"));
        $this->assertNull(QuantityEnum::tryFrom("unknown"));
        $this->assertNull(QuantityEnum::tryFrom(100));
    }

    public function testDecodingFormBackedValue(): void
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::from("RED"));
        $this->assertSame(ColorEnum::green(), ColorEnum::from("GREEN"));

        $this->assertSame(QuantityEnum::single(), QuantityEnum::from(1));
        $this->assertSame(QuantityEnum::double(), QuantityEnum::from(2));
    }

    public function testDecodingFromBackedValueFails(): void
    {
        $this->expectException(\ValueError::class);
        ColorEnum::from("unknown");
    }

    public function testGetValueOf(): void
    {
        $this->assertSame(ColorEnum::red(), ColorEnum::getValueOf(ColorEnum::RED));
        $this->assertSame(ColorEnum::green(), ColorEnum::getValueOf(ColorEnum::GREEN));
        $this->assertSame(ColorEnum::blue(), ColorEnum::getValueOf(ColorEnum::BLUE));
    }

    public function testGetValueForNonExistentBackedValue(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        ColorEnum::getValueOf("incorrect_index");
    }

    public function testGetValues(): void
    {
        $this->assertSame(
            [
                ColorEnum::RED => ColorEnum::red(),
                ColorEnum::GREEN => ColorEnum::green(),
                ColorEnum::BLUE => ColorEnum::blue(),
            ],
            ColorEnum::getValues(),
        );
    }

    public function testCases(): void
    {
        $this->assertSame(
            [ColorEnum::red(), ColorEnum::green(), ColorEnum::blue()],
            ColorEnum::cases(),
        );
    }

    public function testEnumClassMustBeFinal(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageIsOrContains("must be final");
        InvalidNonFinalEnum::cases();
    }

    public function testEnumClassCannotImplementSerializable(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageIsOrContains("cannot be serialized");
        InvalidSerializableEnum::cases();
    }

    public function testInitializationExceptionOnDuplicateIndex(): void
    {
        $this->expectException(\LogicException::class);
        InvalidEnumDuplicatingBackedValues::cases();
    }

    public function testFactoryMethodReturningUnexpectedTypeResultsToError(): void
    {
        $this->expectException(\LogicException::class);
        InvalidReturnTypeEnum::cases();
    }

    public function testFactoryMethodReturningScalarTypeResultsToError(): void
    {
        $this->expectException(\LogicException::class);
        InvalidScalarReturnTypeEnum::cases();
    }

    public function testCloningIsRestricted(): void
    {
        $red = ColorEnum::red();
        $this->expectException(\BadMethodCallException::class);
        $otherRed = clone $red;
    }

    public function testSerializationIsRestricted(): void
    {
        $red = ColorEnum::red();
        $this->expectException(\BadMethodCallException::class);
        serialize($red);
    }

    public function testImplementationOfStringableInterfaceIsDeprecated(): void
    {
        $this->expectUserDeprecationMessage("Implementation of \Stringable is deprecated for enumerable types.");
        DeprecatedStringableEnum::cases();
    }
}
