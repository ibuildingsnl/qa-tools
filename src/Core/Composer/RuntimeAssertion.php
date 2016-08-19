<?php

namespace Ibuildings\QaTools\Core\Composer;

use Assert\Assertion as BaseAssertion;

final class RuntimeAssertion extends BaseAssertion
{
    const PATH_NOT_EXISTS = 2000;

    /**
     * @param string      $path
     * @param string|null $message
     */
    public static function pathNotExists($path, $message = null, $propertyPath = null)
    {
        self::string($path, 'Expected path to be a string, got "%s" of type "%s"');

        if (file_exists($path)) {
            $message = $message ?: 'Expected path "%s" to not exist';

            throw static::createException($path, sprintf($message, $path), self::PATH_NOT_EXISTS, $propertyPath);
        }
    }

    /**
     * @param string $value
     * @param string $message
     * @param int    $code
     * @param string $propertyPath
     * @param array  $constraints
     * @return RuntimeAssertionException
     */
    protected static function createException($value, $message, $code, $propertyPath, array $constraints = array())
    {
        return new RuntimeAssertionException($message, $value);
    }
}
