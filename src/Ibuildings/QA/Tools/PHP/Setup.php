<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\PHP;

use Composer\Script\Event;
use Composer\IO\IOInterface;
use Symfony\Component\Finder\Finder;

class Setup
{

    public static function postInstall(Event $event)
    {
        // check if a config file exists. If so, don't go interactive
        if (file_exists('./ib-qa-tools-php.yml')) {
            static::setupBasedOnConfigFile($event);
        } else {
            static::setupInteractive($event);
        }
    }

    protected static function setupBasedOnConfigFile(Event $event)
    {
    }

    protected static function setupInteractive(Event $event)
    {
        $io = $event->getIO();

        $io->write("Starting setup of Ibuildings QA Tools for PHP");
        if (!$io->askConfirmation("Do you want to continue? [Y/n] ", true)) {
            exit();
        } else {
            $srcPath = $io->askAndValidate(
                "what is the path to the source code? [src] ",
                function ($data) {
                    if (file_exists($data)) return $data; throw new \Exception("That path doesn't exist");
                },
                false,
                'src'
            );
            $testsPath = $io->ask("what is the path to the tests? [tests] ", 'src');
        }
    }
}
