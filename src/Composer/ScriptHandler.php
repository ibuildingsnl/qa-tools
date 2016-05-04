<?php

namespace Ibuildings\QaTools\Composer;

use Composer\Script\Event;
use Exception;

final class ScriptHandler
{
    public static function requireBoxPhar(Event $event)
    {
        $io = $event->getIO();
        $config = $event->getComposer()->getPackage()->getExtra();

        $source = $config['qa-tools-box-source'];
        $installPath = $config['qa-tools-box-install-path'];
        $shaSum = $config['qa-tools-box-sha-sum'];

        if (!$event->isDevMode()) {
            return $io->write('<warning>Not downloading Box in --no-dev mode</warning>');
        }

        if (file_exists($installPath) && sha1_file($installPath) === $shaSum) {
            return $io->write(sprintf(
                '<warning>Expected version of Box is already installed in "%s"</warning>',
                $installPath
            ));
        }

        if (!is_writable(dirname($installPath))) {
            throw new Exception(sprintf(
                'Cannot download Box to "%s": directory is not writable',
                dirname($installPath)
            ));
        }

        $io->write(sprintf('<info>Downloading Box from "%s"</info>', $source));
        $boxContents = file_get_contents($source);

        if ($boxContents === false) {
            $lastError = error_get_last();

            throw new Exception(sprintf(
                'Could not download Box from "%s": %s',
                $source,
                $lastError['message']
            ));
        }

        if (sha1($boxContents) !== $shaSum) {
            throw new Exception(sprintf(
                'Hash of downloaded Box ("%s") does not match expected hash ("%s").',
                sha1($boxContents),
                $shaSum
            ));
        }

        $result = file_put_contents($installPath, $boxContents);

        if ($result === false) {
            $lastError = error_get_last();

            throw new Exception(sprintf(
                'Could not save Box to: "%s": %s',
                $installPath,
                $lastError['message']
            ));
        }

        return $io->write(sprintf('<info>Installed Box at "%s"</info>', $installPath));
    }
}
