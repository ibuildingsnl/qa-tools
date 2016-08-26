<?php

namespace Ibuildings\QaTools\Composer;

use Composer\Script\Event;
use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\Core\Application\ContainerLoader;
use RuntimeException;

final class BuildPharScriptHandler
{
    /**
     * @param Event $event
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public static function buildPhar(Event $event)
    {
        $config = $event->getComposer()->getPackage()->getExtra();
        $io     = $event->getIO();

        $pharIsReadOnly = ini_get('phar.readonly') ? true : false;

        if ($pharIsReadOnly) {
            throw new RuntimeException(
                sprintf(
                    'Cannot build phar: the PHP Phar extension is configured to be read-only.'.PHP_EOL.
                    'Please set phar.readonly to "Off" in your php.ini at: "%s"',
                    php_ini_loaded_file()
                )
            );
        }

        if (!file_exists($config['qa-tools-box-install-path'])) {
            throw new RuntimeException(
                sprintf(
                    'Cannot build phar: file "%s" does not exist',
                    $config['qa-tools-box-install-path']
                )
            );
        }

        if (!is_executable($config['qa-tools-box-install-path'])) {
            throw new RuntimeException(
                sprintf(
                    'Cannot build phar: file "%s" is not executable',
                    $config['qa-tools-box-install-path']
                )
            );
        }

        ContainerLoader::load(new Application(true), true);

        $boxCommand = sprintf('%s build', escapeshellcmd($config['qa-tools-box-install-path']));
        $io->write(
            sprintf('<info>Building phar with "%s"</info>', $boxCommand)
        );
        passthru($boxCommand, $exitCode);

        if ($exitCode !== 0) {
            $io->writeError([
                '<error>' .
                '                                                                                  ',
                '  Phar build failed. If the error contains "Too many open files", you can try to  ',
                '  increase the open file limit of your system:                                    ',
                '                                                                                  ',
                '      ulimit -Sn 4096                                                             ',
                '                                                                                  ',
                '      See https://github.com/box-project/box2/issues/80#issuecomment-76630852     ',
                '                                                                                  ',
                '</error>',
            ]);
            exit(1);
        }

        $io->write(
            sprintf('<info>Phar built successfully!</info>', $boxCommand)
        );
    }
}
