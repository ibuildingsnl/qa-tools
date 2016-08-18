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
    const PATH_NOT_EXISTS            = 1002;

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
     * @param string      $path
     * @param string|null $message
     * @param string|null $propertyPath
     */
    public static function pathNotExists($path, $message = null, $propertyPath = null)
    {
        self::string($path, 'Expected path to be a string, got "%s" of type "%s"');

        if (file_exists($path)) {
            $message = $message ?: 'Expected path "%s" to not exist';

            throw static::createException(
                $path,
                sprintf($message, $path),
                static::PATH_NOT_EXISTS,
                $propertyPath
            );
        }
    }
}
