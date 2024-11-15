<?php

namespace Frej\Optional\Tests;

use Frej\Optional\Exception\OptionNoneUnwrappedException;
use Frej\Optional\None;
use Frej\Optional\Some;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NoneTest extends TestCase
{
    public function testIsEmpty(): void
    {
        $option = None::make();

        $this->assertTrue($option->isEmpty());
    }

    public function testIsSome(): void
    {
        $option = None::make();

        $this->assertFalse($option->isSome());
    }

    public function testIsNone(): void
    {
        $option = None::make();

        $this->assertTrue($option->isNone());
    }

    public function testThrowsOnUnwrap(): void
    {
        $option = None::make();
        $this->expectException(OptionNoneUnwrappedException::class);
        $option->unwrap();
    }

    public function testThrowsCustomError(): void
    {
        $option = None::make();
        $this->expectException(OptionNoneUnwrappedException::class);
        $this->expectExceptionMessage('my test msg');
        $option->unwrap('my test msg');
    }

    public function testThrowsCustomException(): void
    {
        $option = None::make();

        $exception = new class () extends \Exception {};

        $this->expectException($exception::class);
        $option->unwrap($exception);
    }

    public function testUnwrapOr(): void
    {
        $option = None::make();

        $default = 'hallo';

        $r = $option->unwrapOr($default);
        $this->assertEquals($default, $r);

        $r = $option->unwrapOr(static fn () => $default);
        $this->assertEquals($default, $r);
    }

    public function testUnwrapOrNull(): void
    {
        $option = None::make();

        $this->assertNull($option->unwrapOrNull());
    }

    public function testUnwrapInto(): void
    {
        $option = None::make();

        $this->expectException(OptionNoneUnwrappedException::class);
        $option->unwrapInto(fn () => $this->assertFalse(true, 'unwrapInto should not call the callback for None'));
    }

    public function testUnwrapIntoOr(): void
    {
        $option = None::make();

        $option->unwrapIntoOr(function ($v) {
            $this->assertEquals('a', $v);
        }, 'a');
    }

    public function testFilter(): void
    {
        $option = None::make();

        $this->assertInstanceOf(None::class, $option->filter('whatever'));
    }

    public function testFiterInto(): void
    {
        $option = None::make();

        $option->filterInto(
            callback: function ($v) {
                $this->assertInstanceOf(None::class, $v);
            },
            predicate: 'whatever'
        );
    }

    public function testMap(): void
    {
        $option = None::make();

        $this->assertInstanceOf(None::class, $option->map(fn () => $this->assertFalse(true, 'map should not call the transformer for None')));
    }

    public function testMapOr(): void
    {
        $option = None::make();

        $r = $option->mapOr(fn () => $this->assertFalse(true, 'mapOr should not call the transformer for None'), true);

        $this->assertInstanceOf(Some::class, $r);
        $this->assertEquals(true, $r->unwrap());
    }

    public function testMapInto(): void
    {
        $option = None::make();

        $option->mapInto(
            callback: fn ($v) => $this->assertInstanceOf(None::class, $v),
            transformer: fn () => $this->assertFalse(true, 'mapInto should not call the transformer for None')
        );
    }

    public function testMapIntoOr(): void
    {
        $option = None::make();

        $option->mapIntoOr(
            callback: function ($v) {
                $this->assertInstanceOf(Some::class, $v);
                $this->assertEquals(true, $v->unwrap());
            },
            transformer: fn ($v) => $this->assertFalse(true, 'mapIntoOr should not call the transformer for None'),
            default: 'me'
        );
    }

    public function testSingletonNoneObjectIsCreatedWhenNoneCalled(): void
    {
        $noneOption1 = None::make();
        $noneOption2 = None::make();
        $this->assertSame($noneOption1, $noneOption2);
        $this->assertTrue($noneOption1->isNone());
    }

    public function testNonePropertyIsInstantiatedAfterConstructCall(): void
    {
        $reflection = new ReflectionClass(None::class);
        $noneProperty = $reflection->getProperty('singleton');
        $noneProperty->setAccessible(true);

        // Unset none for the next test
        $noneProperty->setValue(null, null);

        // Check after calling None method
        $noneOption = None::make();
        $this->assertInstanceOf(None::class, $noneProperty->getValue($noneOption));

        // Unset none for the next tests
        $noneProperty->setValue(null, null);
    }
}
