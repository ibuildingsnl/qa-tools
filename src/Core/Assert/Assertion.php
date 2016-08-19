<?php

namespace Ibuildings\QaTools\Core\Assert;

use Assert\Assertion as BaseAssertion;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;

/**
 * @method static void nullOrNonEmptyString($value, $propertyPath = null)
 * @method static void allNonEmptyString($value, $propertyPath = null)
 * @method static void nullOrallNonEmptyString($value, $propertyPath = null)
 */
final class Assertion extends BaseAssertion
{
    const INVALID_NON_EMPTY_STRING   = 1001;

    protected static $exceptionClass = InvalidArgumentException::class;

    /**
     * @param string $value
     * @param string $propertyPath
     * @return void
     */
    public static function nonEmptyString($value, $propertyPath)
    {
        if (!is_string($value) || trim($value) === '') {
            $message = 'Expected non-empty string for "%s", "%s" given';

            throw static::createException(
                $value,
                sprintf($message, $propertyPath, static::stringify($value)),
                static::INVALID_NON_EMPTY_STRING,
                $propertyPath
            );
        }
    }
}
