<?php

namespace Frej\Optional;

use Frej\Optional\Exception\OptionNoneUnwrappedException;

/**
 * Class Option
 *
 * Represents an optional value that may or may not be present.
 * It Can be used in places where null or empty values cannot be
 * used for determining whether the value is actually set.
 *
 * {@see Option::None()} returns static never-changing object instance.
 * This is useful in a way that you can compare variable against
 * the return value of that function.
 *
 * @template T
 */
abstract class Option
{
    /**
     * Construct an option of Some(T)
     *
     * @template V
     * @param V $val
     * @return Option<V>
     */
    public static function Some(mixed $val): self
    {
        return Some::make($val);
    }

    /**
     * Construct an option of None
     *
     * Returns a static singleton instance of Option.
     * That means it is possible to just compare variables
     * against this instance without calling functions like
     * {@see Option::isSome()} and {@see Option::isNone()}
     *
     * @template V
     * @return Option<V>
     */
    public static function None(): self
    {
        return None::make();
    }

    /**
     * Check if option is Some
     *
     * @return bool
     */
    abstract public function isSome(): bool;

    /**
     * Check if option is None
     *
     * @return bool
     */
    abstract public function isNone(): bool;

    /**
     * Check if the wrapped value is empty
     *
     * Returns true if option is None.
     * Shorthand for `empty($option->unwrapOrNull()
     *
     * @return bool
     */
    abstract public function isEmpty(): bool;

    /**
     * Retrieve the wrapped value or throw an exception if the option is None
     *
     * For an alternative with option for specifying the exception message {@see Option::expect()}
     *
     * @return T The value of the option if it is not none
     * @throws OptionNoneUnwrappedException if the option is none
     */
    abstract public function unwrap(null|string|\Throwable $error = null): mixed;

    /**
     * Retrieve the wrapped value or the value passed as default
     *
     * @param T|callable(): T $default Value to be used as fallback when option is not Some. When callable is provided, it will be called to resolve the fallback value instead.
     * @return T
     */
    abstract public function unwrapOr(mixed $default): mixed;

    /**
     * Retrieve the wrapped value or null
     *
     * @return T|null
     */
    abstract public function unwrapOrNull(): mixed;

    /**
     * Retrieve the value and pass it into the callback. Callback is not called if option is None
     *
     * @param callable(T): void $callback
     */
    abstract public function unwrapInto(callable $callback, null|string|\Throwable $error = null): void;

    /**
     * Retrieve the value and pass it into the callback.
     *
     * @param callable(T): void $callback
     * @param T|callable():T $default Value to be used as fallback when option is not Some. When callable is provided, it will be called to resolve the fallback value instead.
     */
    abstract public function unwrapIntoOr(callable $callback, mixed $default): void;

    /**
     * Filter Some(T) using a predicate
     *
     * Calls the provided predicate function on the contained value to check if the Option is Some(T), and returns Some(T) if the function returns true; otherwise, returns None
     *
     * @param T|callable(T): bool $predicate Provided predicate lambda. If returns true, returned value will be Some(T), otherwise None
     * @return Option<T>
     */
    abstract public function filter(mixed $predicate): Option;

    /**
     * Filter Some(T) using a predicate
     *
     * Calls the provided predicate function on the contained value to check if the Option is Some(T), and retuns Some(T) if the function returns treu; otherwise returns None
     *
     * @param callable(Option<T>): void $callback
     * @param T|callable(T): bool $predicate Provided predcate lambda. If returns true, returned value will be Some(T),
     * otherwise None
     */
    abstract public function filterInto(callable $callback, mixed $predicate): void;

    /**
     * Tranform Option<T> to Option<U> using provided the function
     *
     * Leaves None unchanged
     *
     * @template U
     * @param callable(T): U $transformer
     * @return Option<U>
     */
    abstract public function map(callable $transformer): Option;

    /**
     * Tranform Option<T> to Option<U> using provided the function
     *
     * Leaves None unchanged
     *
     * @template U
     * @param callable(Option<U>): void $callback
     * @param callable(T): U $transformer
     */
    abstract public function mapInto(callable $callback, callable $transformer): void;

    /**
     * Tranforms Option<T> to Option<U> using the provided function, uses `$default` value on None
     *
     * @template U
     * @param callable(T): U $transformer
     * @param U|callable(): U $default fallback value
     * @return Option<U>
     */
    abstract public function mapOr(callable $transformer, mixed $default): Option;

    /**
     * Tranforms Option<T> to Option<U> using the provided function, uses `$default` value on None
     * and inputs the value into the provided callback
     *
     * @template U
     * @param callable(Option<U>): void $callback
     * @param callable(T): U $transformer
     * @param U|callable(): U $default fallback value
     */
    abstract public function mapIntoOr(callable $callback, callable $transformer, mixed $default): void;

    /**
     * Unwraps inner value into $dst if $self is Some.
     *
     * This can be effectively used in if construct in the following way:
     *
     * $myOption = Option::Some('my option val');
     *
     * If (Option::letSome($v, $myOption)) {
     *  echo $v; // echoes "my option val" if $muOption is Some
     * }
     *
     * @template A
     * @param A &$dst The variable to be set with the unwrapped value of the Option if it's Some.
     * @param Option<A> $self The Option object to check if it is Some.
     *
     * @return bool Returns true if $self is Some and $dst is set, otherwise returns false.
     */
    public static function letSome(mixed &$dst, Option $self): bool
    {
        if ($self->isSome()) {
            $dst = $self->val;
            return true;
        }

        return false;
    }
}
