<?php

namespace Frej\Optional\Tests;

use Frej\Optional\Exception\OptionNoneUnwrappedException;
use Frej\Optional\None;
use Frej\Optional\Some;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function PHPUnit\Framework\returnSelf;

class SomeTest extends TestCase
{
    public function testIsEmpty(): void
    {
        $option = Some::make('a');

        $this->assertFalse($option->isEmpty());

        $option = Some::make('');
        $this->assertTrue($option->isEmpty());
    }

    public function testIsSome(): void
    {
        $option = Some::make('a');

        $this->assertTrue($option->isSome());
    }

    public function testIsNone(): void
    {
        $option = Some::make('a');

        $this->assertFalse($option->isNone());
    }

    public function testUnwrap(): void
    {
        $option = Some::make('a');
        $this->assertEquals('a', $option->unwrap());
    }

    public function testUnwrapOr(): void
    {
        $initial = 'a';
        $option = Some::make($initial);

        $default = 'hallo';

        $r = $option->unwrapOr($default);
        $this->assertEquals($initial, $r);

        $r = $option->unwrapOr(static fn () => $default);
        $this->assertEquals($initial, $r);
    }

    public function testUnwrapOrNull(): void
    {
        $option = Some::make('a');

        $r = $option->unwrapOrNull();

        $this->assertEquals('a', $r);
    }

    public function testUnwrapInto(): void
    {
        $val = 'a';
        $option = Some::make($val);

        $option->unwrapInto(fn ($v) => $this->assertEquals($val, $v));
    }

    public function testUnwrapIntoOr(): void
    {
        $val = 'a';
        $option = Some::make($val);

        $option->unwrapIntoOr(function ($v) {
            $this->assertEquals('a', $v);
        }, 'a');
    }

    public function testFilter(): void
    {
        $option = Some::make('a');

        $r = $option->filter('whatever');
        $this->assertInstanceOf(None::class, $r);

        $r = $option->filter('a');
        $this->assertInstanceOf(Some::class, $r);
        $this->assertEquals('a', $r->unwrap());
        $this->assertSame($option, $r);

        $r = $option->filter(function ($v) {
            $this->assertEquals('a', $v, 'filter should pass the wrapped value into the callback');
            return false;
        });
        $this->assertInstanceOf(None::class, $r);

        $r = $option->filter(fn ($v) => true);
        $this->assertInstanceOf(Some::class, $r);
        $this->assertEquals('a', $r->unwrap());
        $this->assertSame($option, $r);
    }

    public function testFilterInto(): void
    {
        $option = Some::make('a');

        $option->filterInto(
            callback: function ($v) {
                $this->assertInstanceOf(None::class, $v);
            },
            predicate: 'whatever'
        );

        $option->filterInto(
            callback: function ($v) use ($option) {
                $this->assertInstanceOf(Some::class, $v);
                $this->assertEquals('a', $v->unwrap());
                $this->assertSame($option, $v);
            },
            predicate: 'a'
        );

        $option->filterInto(
            callback: function ($v) {
                $this->assertInstanceOf(None::class, $v);
            },
            predicate: function ($v) {
                $this->assertEquals('a', $v, 'filter should pass the wrapped value into the callback');
                return false;
            }
        );

        $option->filterInto(
            callback: function ($v) use ($option) {
                $this->assertInstanceOf(Some::class, $v);
                $this->assertEquals('a', $v->unwrap());
                $this->assertSame($option, $v);
            },
            predicate: fn ($v) => true
        );
    }

    public function testMap(): void
    {
        $option = Some::make('a');

        $r = $option->map(function ($v) {
            $this->assertEquals('a', $v);
            return 20;
        });
        $this->assertInstanceOf(Some::class, $r);
        $this->assertEquals(20, $r->unwrap());
        $this->assertNotSame($option, $r);
    }

    public function testMapOr(): void
    {
        $option = Some::make('a');

        $r = $option->mapOr(function ($v) {
            $this->assertEquals('a', $v);
            return 20;
        }, 1);

        $this->assertInstanceOf(Some::class, $r);
        $this->assertEquals(20, $r->unwrap());
        $this->assertNotSame($option, $r);
    }

    public function testMapInto(): void
    {
        $option = Some::make('a');

        $option->mapInto(
            callback: function ($v) use ($option) {
                $this->assertInstanceOf(Some::class, $v);
                $this->assertEquals(20, $v->unwrap());
                $this->assertNotSame($option, $v);
            },
            transformer: function ($v) {
                $this->assertEquals('a', $v);
                return 20;
            }
        );
    }

    public function testMapIntoOr(): void
    {
        $option = Some::make('a');

        $option->mapIntoOr(
            callback: function ($v) use ($option) {
                $this->assertInstanceOf(Some::class, $v);
                $this->assertEquals(20, $v->unwrap());
                $this->assertNotSame($option, $v);
            },
            transformer: function ($v) {
                $this->assertEquals('a', $v);
                return 20;
            },
            default: 'me'
        );
    }
}
