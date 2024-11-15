<?php

namespace Frej\Optional;

use Frej\Optional\Exception\OptionNoneUnwrappedException;
use Throwable;

/**
 * @template T
 * @extends Option<T>
 * @internal
 */
class None extends Option
{
    protected static ?self $singleton = null;

    protected function __construct()
    {
    }

    /**
     * @template A
     * @return Option<A>
     */
    public static function make(): Option
    {
        if (self::$singleton === null) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /**
     * @inheritdoc
     */
    public function isSome(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isNone(): bool
    {
        return true;
    }


    public function isEmpty(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function unwrap(null|string|\Throwable $error = null): mixed
    {
        if ($error instanceof \Throwable) {
            throw $error;
        }
        throw new OptionNoneUnwrappedException($error ?? "Option is none");
    }

    /**
     * @inheritdoc
     */
    public function unwrapOr(mixed $default): mixed
    {
        if (is_callable($default)) {
            return $default();
        }
        return $default;
    }

    /**
     * @inheritdoc
     */
    public function unwrapOrNull(): mixed
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function unwrapInto(callable $callback, null|string|\Throwable $error = null): void
    {
        $this->unwrap($error);
    }

    /**
     * @inheritdoc
     */
    public function unwrapIntoOr(callable $callback, mixed $default): void
    {
        $callback($this->unwrapOr($default));
    }

    /**
     * @inheritdoc
     */
    public function filter(mixed $predicate): Option
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function filterInto(callable $callback, mixed $predicate): void
    {
        $callback($this);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformer): Option
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function mapOr(callable $transformer, mixed $default): Option
    {
        if (is_callable($default)) {
            return Some::make($default());
        }
        return Some::make($default);
    }

    public function mapInto(callable $callback, callable $transformer): void
    {
        $callback($this->map($transformer));
    }

    public function mapIntoOr(callable $callback, callable $transformer, mixed $default): void
    {
        $callback($this->mapOr($transformer, $default));
    }
}
