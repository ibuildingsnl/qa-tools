<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Exception\RuntimeException;

final class Sigints
{
    /** @var bool */
    private static $registered = false;
    /** @var bool */
    private static $receivedSignal = false;

    /**
     * @return void
     */
    public static function trap()
    {
        if (self::$registered) {
            throw new RuntimeException('Signal handler already registered');
        }
        if (!pcntl_signal(SIGINT, [self::class, 'handle'])) {
            throw new RuntimeException(
                sprintf('Could not register signal handler ("%s")', @error_get_last()['message'])
            );
        }

        self::$receivedSignal = false;
        self::$registered = true;
    }

    /**
     * Relinquish control to PHP.
     *
     * @return void
     */
    public static function resetTrap()
    {
        if (!self::$registered) {
            throw new RuntimeException('Signal handler has not been registered');
        }
        if (!pcntl_signal_dispatch()) {
            throw new RuntimeException(
                sprintf('Call to pcntl_signal_dispatch() failed (PHP error: "%s")', @error_get_last()['message'])
            );
        }
        if (!pcntl_signal(SIGINT, SIG_DFL)) {
            throw new RuntimeException(
                sprintf(
                    'Could not relinquish control to PHP by registering default signal handler (PHP error: "%s")',
                    @error_get_last()['message']
                )
            );
        }

        self::$registered = false;
    }

    /**
     * Returns whether one or more SIGINTs were trapped since trapping started.
     * @see {Sigints::trap()}
     *
     * @return bool
     */
    public static function wereTrapped()
    {
        if (!pcntl_signal_dispatch()) {
            throw new RuntimeException(
                sprintf('Call to pcntl_signal_dispatch() failed (PHP error: "%s")', @error_get_last()['message'])
            );
        }

        return self::$receivedSignal;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod) -- @see Sigints::trap()
     * @return void
     */
    private static function handle()
    {
        self::$receivedSignal = true;
    }
}
