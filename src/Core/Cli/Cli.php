<?php

namespace Ibuildings\QaTools\Core\Cli;

use Symfony\Component\Process\ProcessBuilder;
use Webmozart\Assert\Assert;

class Cli
{
    /** @var array */
    private static $installedExecutables = [];

    /**
     * Check whether an executable is installed on the local system
     * Assumes a linux system with the `which` binary installed.
     *
     * @param string $executable
     * @return bool
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public static function isExecutableInstalled($executable)
    {
        Assert::string($executable, 'Executable must be a string');

        if (!array_key_exists($executable, self::$installedExecutables)) {
            $process = ProcessBuilder::create(['which', $executable])->getProcess();
            self::$installedExecutables[$executable] = $process->run() === 0;
        }

        return self::$installedExecutables[$executable];
    }
}
