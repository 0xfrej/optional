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
class Option
{
    protected static ?self $none = null;

    /**
     * @var T $val
     */
    protected readonly mixed $val;

    protected function __construct() {}

    /**
     * Construct an option of Some(T)
     *
     * @template V
     * @param V $val
     * @return Option<V>
     */
    public static function Some(mixed $val): self
    {
        if (!isset(self::$none)) {
            self::$none = new static();
        }
        $o = new self();
        $o->val = $val;
        return $o;
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
        if (!isset(self::$none)) {
            self::$none = new static(null);
        }
        return self::$none;
    }

    /**
     * Check if option is Some
     *
     * @return bool
     */
    public function isSome(): bool
    {
        return $this !== self::$none;
    }

    /**
     * Check if option is None
     *
     * @return bool
     */
    public function isNone(): bool
    {
        return $this === self::$none;
    }

    /**
     * Retrieve the wrapped value or throw an exception if the option is None
     *
     * For an alternative with no message to be set {@see Option::unwrap()}
     *
     * @param string|null $msg The message to be thrown when the option is none
     * @return T The value of the option if it is not none
     * @throws OptionNoneUnwrappedException if the option is none
     */
    public function expect(?string $msg): mixed
    {
        if ($this->isSome()) {
            return $this->val;
        }
        throw new OptionNoneUnwrappedException($msg ?? "Option is none");
    }

    /**
     * Retrieve the wrapped value or throw an exception if the option is None
     *
     * For an alternative with option for specifying the exception message {@see Option::expect()}
     *
     * @return T The value of the option if it is not none
     * @throws OptionNoneUnwrappedException if the option is none
     */
    public function unwrap(): mixed
    {
        return $this->expect(null);
    }

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
