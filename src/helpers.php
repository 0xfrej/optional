<?php

namespace Frej\Optional;

/**
 * Unwraps inner value into $dst if $self is Some.
 *
 * This can be effectively used in if construct in the following way:
 *
 * $myOption = Some('my option val');
 *
 * if (letSome($v, $myOption)) {
 *  echo $v; // echoes "my option val" if $muOption is Some
 * }
 *
 * @template A
 * @param A &$dst The variable to be set with the unwrapped value of the Option if it's Some.
 * @param Option<A> $option The Option object to check if it is Some.
 *
 * @return bool Returns true if $self is Some and $dst is set, otherwise returns false.
 */
function letSome(mixed &$dst, Option $option): bool
{
    return $option::letSome($dst, $option);
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
function None(): Option
{
    return Option::None();
}

/**
 * Construct an option of Some(T)
 *
 * @template V
 * @param V $val
 * @return Option<V>
 */
function Some(mixed $val): Option
{
    return Option::Some($val);
}
