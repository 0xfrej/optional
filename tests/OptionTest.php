<?php

namespace Frej\Optional\Tests;

use Frej\Optional\Exception\OptionNoneUnwrappedException;
use Frej\Optional\Option;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class OptionTest extends TestCase
{
    public function testOptionSomeIsNotNone(): void
    {
        $option = Option::Some(null);
        $this->assertFalse($option->isNone());
        $this->assertTrue($option->isSome());
    }

    public function testOptionNoneIsNotSome(): void
    {
        $option = Option::None();
        $this->assertTrue($option->isNone());
        $this->assertFalse($option->isSome());
    }

    public function testOptionNoneThrowsOnUnwrap(): void
    {
        $option = Option::None();
        $this->expectException(OptionNoneUnwrappedException::class);
        $option->unwrap();
    }

    public function testOptionNoneThrowsOnExpect(): void
    {
        $option = Option::None();
        $this->expectException(OptionNoneUnwrappedException::class);
        $option->expect("MyMessage");
        $this->expectExceptionMessage("MyMessage");
    }

    /**
     * Tests if the Option object will be created with a single value
     *
     * @return void
     */
    public function testOptionCreatedWithSingleValue(): void
    {
        $option = Option::Some('single value');
        $this->assertTrue($option->isSome());
        $this->assertSame('single value', $option->unwrap());
        $this->assertSame('single value', $option->expect("MyMessage"));
    }

    /**
     * Tests if singleton None object is created when None method is called
     *
     * @return void
     */
    public function testSingletonNoneObjectIsCreatedWhenNoneCalled(): void
    {
        $noneOption1 = Option::None();
        $noneOption2 = Option::None();
        $this->assertSame($noneOption1, $noneOption2);
        $this->assertTrue($noneOption1->isNone());
    }

    /**
     * Tests if the $none property is instantiated after construct and None call if
     * it was not instantiated yet
     *
     * @return void
     */
    public function testNonePropertyIsInstantiatedAfterConstructAndNoneCall(): void
    {
        $reflection = new ReflectionClass(Option::class);
        $noneProperty = $reflection->getProperty('none');
        $noneProperty->setAccessible(true);

        // Unset none for the test
        $noneProperty->setValue(null);

        // Check after constructing Some option
        $someOption = Option::Some('some value');
        $this->assertInstanceOf(Option::class, $noneProperty->getValue($someOption));

        // Unset none for the next test
        $noneProperty->setValue(null);

        // Check after calling None method
        $noneOption = Option::None();
        $this->assertInstanceOf(Option::class, $noneProperty->getValue($noneOption));

        // Unset none for the next tests
        $noneProperty->setValue(null);
    }
}
