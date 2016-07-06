<?php

namespace Ibuildings\QaTools\Composer;

use Composer\Script\Event;
use Exception;
use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\Core\Application\ContainerLoader;

final class PharScriptHandler
{
    public static function requireBoxPhar(Event $event)
    {
        $io     = $event->getIO();
        $config = $event->getComposer()->getPackage()->getExtra();

        $source      = $config['qa-tools-box-source'];
        $installPath = $config['qa-tools-box-install-path'];
        $shaSum      = $config['qa-tools-box-sha-sum'];

        if (!$event->isDevMode()) {
            return $io->write('<warning>Not downloading Box in --no-dev mode</warning>');
        }

        if (file_exists($installPath) && sha1_file($installPath) === $shaSum) {
            return $io->write(
                sprintf(
                    '<warning>Expected version of Box is already installed in "%s"</warning>',
                    $installPath
                )
            );
        }

        if (!is_writable(dirname($installPath))) {
            throw new Exception(
                sprintf(
                    'Cannot download Box to "%s": directory is not writable',
                    dirname($installPath)
                )
            );
        }

        $io->write(sprintf('<info>Downloading Box from "%s"</info>', $source));
        $boxContents = file_get_contents($source);

        if ($boxContents === false) {
            $lastError = error_get_last();

            throw new Exception(
                sprintf(
                    'Could not download Box from "%s": %s',
                    $source,
                    $lastError['message']
                )
            );
        }

        if (sha1($boxContents) !== $shaSum) {
            throw new Exception(
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

            throw new Exception(
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

            throw new Exception(
                sprintf(
                    'Unable to assign execute permissions to "%s", current octal permissions "%o" ("%s")',
                    $installPath,
                    $permissions,
                    $lastError['message']
                )
            );
        }

        return $io->write(sprintf('<info>Installed Box at "%s".</info>', $installPath));
    }

    public static function buildPhar(Event $event)
    {
        $config = $event->getComposer()->getPackage()->getExtra();

        $pharIsReadOnly = ini_get('phar.readonly') ? true : false;

        if ($pharIsReadOnly) {
            throw new Exception(
                sprintf(
                    'Cannot build phar: the PHP Phar extension is configured to be read-only.'.PHP_EOL.
                    'Please set phar.readonly to "Off" in your php.ini at: "%s"',
                    php_ini_loaded_file()
                )
            );
        }

        if (!file_exists($config['qa-tools-box-install-path'])) {
            throw new Exception(
                sprintf(
                    'Cannot build phar: file "%s" does not exist',
                    $config['qa-tools-box-install-path']
                )
            );
        }

        if (!is_executable($config['qa-tools-box-install-path'])) {
            throw new Exception(
                sprintf(
                    'Cannot build phar: file "%s" is not executable',
                    $config['qa-tools-box-install-path']
                )
            );
        }

        // Precompile the container
        $event->getIO()->write('<info>Precompiling the container</info>');
        ContainerLoader::load(new Application(true), true);

        $boxCommand = sprintf('%s build -vv', escapeshellcmd($config['qa-tools-box-install-path']));
        $event->getIO()->write(
            sprintf('<info>Building phar with "%s"</info>', $boxCommand)
        );
        passthru($boxCommand, $exitCode);

        if ($exitCode !== 0) {
            $event->getIO()->writeError([
                '<error>' .
                '                                                                                                    ',
                ' Phar build failed. If the error is "", you can try to increase the open file limit of your system: ',
                '                                                                                                    ',
                '     ulimit -Sn 4096                                                                                ',
                '                                                                                                    ',
                '     See https://github.com/box-project/box2/issues/80#issuecomment-77322046                        ',
                '                                                                                                    ',
                '</error>',
            ]);
        }
    }
}
