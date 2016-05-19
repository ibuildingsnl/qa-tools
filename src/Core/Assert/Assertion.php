<?php

namespace Ibuildings\QaTools\Core\Assert;

use Assert\Assertion as BaseAssertion;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;

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

    /**
     * @param mixed $value
     * @param string $propertyPath
     * @return void
     */
    public static function instanceOfEither($value, array $classes, $propertyPath)
    {
        if (get_class($value) === false || !in_array(get_class($value), $classes)) {
            $message = 'Expected an instance of "%s" for "%s", "%s" given';

            throw static::createException(
                $value,
                sprintf($message, implode('|', $classes), $propertyPath, get_class($value) ?: gettype($value)),
                static::INVALID_NON_EMPTY_STRING,
                $propertyPath
            );
        }
    }
}
