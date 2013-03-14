<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\PHP;

use Composer\Script\Event;
use Composer\IO\IOInterface;

class Setup
{

    public static function postInstall(Event $event)
    {
        $io = $event->getIO();

        if (!$io->isInteractive()) {
            static::setupBasedOnCliInteraction($event);
        } else {
            static::setupBasedOnConfigFile($event);
        }
    }

    protected static function setupBasedOnConfigFile(Event $event)
    {
        throw new \Exception("Setup based on config file not implemented");
    }

    protected static function setupBasedOnCliInteraction(Event $event)
    {
        $io = $event->getIO();

        $io->write("Starting setup of Ibuildings QA Tools for PHP");
        if (!$io->askConfirmation("Do you want to continue")) {
            exit();
        } else {
            
        }
    }
}
