<?php

namespace Frej\Optional;

/**
 * @template T
 * @extends Option<T>
 */
class Some extends Option
{
    /**
     * @param T $val
     */
    protected function __construct(
        protected readonly mixed $val
    ) {
    }

    /**
     * @template A
     * @param A $val
     * @return Option<A>
     */
    public static function make(mixed $val): Option
    {
        return new self($val);
    }

    /**
     * @inheritdoc
     */
    public function isSome(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isNone(): bool
    {
        return false;
    }

    public function isEmpty(): bool
    {
        return empty($this->val);
    }

    /**
     * @inheritdoc
     */
    public function unwrap(null|string|\Throwable $error = null): mixed
    {
        return $this->val;
    }

    /**
     * @inheritdoc
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $this->val;
    }

    /**
     * @inheritdoc
     */
    public function unwrapOrNull(): mixed
    {
        return $this->val;
    }

    /**
     * @inheritdoc
     */
    public function unwrapInto(callable $callback, null|string|\Throwable $error = null): void
    {
        $callback($this->unwrap($error));
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
        if (is_callable($predicate) && $predicate($this->val) === true) {
            return $this;
        }
        if ($predicate == $this->val) {
            return $this;
        }
        return None::make();
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformer): Option
    {
        return self::make($transformer($this->val));
    }

    /**
     * @inheritdoc
     */
    public function mapOr(callable $transformer, mixed $default): Option
    {
        return self::make($transformer($this->val));
    }
}
