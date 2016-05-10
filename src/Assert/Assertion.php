<?php

namespace Ibuildings\QaTools\Assert;

use Assert\Assertion as BaseAssertion;
use Ibuildings\QaTools\Exception\InvalidArgumentException;

final class Assertion extends BaseAssertion
{
    const INVALID_NON_EMPTY_STRING  = 1001;
    const INVALID_STRING_OR_INTEGER = 1002;


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
