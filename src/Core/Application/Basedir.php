<?php

namespace Ibuildings\QaTools\Core\Application;

use Ibuildings\QaTools\Core\Assert\Assertion as Assert;
use Ibuildings\QaTools\Core\Exception\RuntimeException;

/**
 * Contains the application base directory during run-time. It helps prevent
 * usages of absolute paths, which will cause problems when that absolute
 * path doesn't exist, ie. on other users' machines.
 *
 * Set it to the `./bin` directory whenever parts of the application are used.
 */
final class Basedir
{
    /** @var string|null */
    private static $directory;

    /**
     * @return string
     */
    public static function get()
    {
        if (self::$directory === null) {
            throw new RuntimeException('Application basedir has not been set');
        }

        return self::$directory;
    }

    /**
     * @param string $directory
     */
    public static function set($directory)
    {
        Assert::string($directory, 'Base directory ought to be a string, got "%s" of type "%s"');

        if (self::$directory !== null) {
            throw new RuntimeException(
                sprintf(
                    'Application basedir has already been set to "%s" and may not change during run-time',
                    self::$directory
                )
            );
        }

        self::$directory = $directory;
    }
}
