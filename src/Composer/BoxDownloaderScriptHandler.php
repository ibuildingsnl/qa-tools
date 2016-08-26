<?php

namespace Ibuildings\QaTools\Composer;

use Composer\Script\Event;
use RuntimeException;

final class BoxDownloaderScriptHandler
{
    public static function downloadBoxPhar(Event $event)
    {
        $io     = $event->getIO();
        $config = $event->getComposer()->getPackage()->getExtra();

        $source      = $config['qa-tools-box-source'];
        $installPath = $config['qa-tools-box-install-path'];
        $shaSum      = $config['qa-tools-box-sha-sum'];

        if (!$event->isDevMode()) {
            return $io->writeError('<warning>Not downloading Box in --no-dev mode</warning>');
        }

        if (file_exists($installPath) && sha1_file($installPath) === $shaSum) {
            return $io->writeError(
                sprintf(
                    '<warning>Expected version of Box is already installed in "%s"</warning>',
                    $installPath
                )
            );
        }

        if (!is_writable(dirname($installPath))) {
            throw new RuntimeException(
                sprintf(
                    'Cannot download Box to "%s": directory is not writable',
                    dirname($installPath)
                )
            );
        }

        $io->writeError(sprintf('<info>Downloading Box from "%s"</info>', $source));
        $boxContents = file_get_contents($source);

        if ($boxContents === false) {
            $lastError = error_get_last();

            throw new RuntimeException(
                sprintf(
                    'Could not download Box from "%s": %s',
                    $source,
                    $lastError['message']
                )
            );
        }

        if (sha1($boxContents) !== $shaSum) {
            throw new RuntimeException(
                sprintf(
                    'Hash of downloaded Box ("%s") does not match expected hash ("%s").',
                    sha1($boxContents),
                    $shaSum
                )
            );
        }

        $result = file_put_contents($installPath, $boxContents);

        if ($result === false) {
            $lastError = error_get_last();

            throw new RuntimeException(
                sprintf(
                    'Could not save Box to: "%s": %s',
                    $installPath,
                    $lastError['message']
                )
            );
        }

        if (!chmod($installPath, 0775)) {
            $permissions = fileperms($installPath);
            $lastError   = error_get_last();

            throw new RuntimeException(
                sprintf(
                    'Unable to assign execute permissions to "%s", current octal permissions "%o" ("%s")',
                    $installPath,
                    $permissions,
                    $lastError['message']
                )
            );
        }

        return $io->writeError(sprintf('<info>Installed Box at "%s".</info>', $installPath));
    }
}
