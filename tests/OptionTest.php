<?php

namespace Frej\Optional\Tests;

use Frej\Optional\Option;
use Frej\Optional\Some;
use Frej\Optional\None;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testOptionSomeIsNotNone(): void
    {
        $option = Option::Some(null);
        $this->assertInstanceOf(Some::class, $option);
        $this->assertFalse($option->isNone());
        $this->assertTrue($option->isSome());
    }

    public function testOptionNoneIsNotSome(): void
    {
        $option = Option::None();
        $this->assertInstanceOf(None::class, $option);
        $this->assertTrue($option->isNone());
        $this->assertFalse($option->isSome());
    }


    public function testOptionLetSome(): void
    {
        $r = Option::letSome($a, Option::Some('a'));
        $this->assertTrue($r, 'Option::letSome() should not return false when the option is Some');
        $this->assertEquals('a', $a);

        $r = Option::letSome($b, Option::None());
        $this->assertFalse($r, 'Option::letSome() should not return true when the option is None');
        $this->assertFalse(isset($b), 'variable should not be set after calling Option::letSome() with None');
    }
}
