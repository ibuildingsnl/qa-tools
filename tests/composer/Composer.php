<?php

namespace Ibuildings\QaTools\ComposerTest;

use Composer\Json\JsonManipulator;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

final class Composer
{
    /**
     * Mocks Composer repositories, disabling Packagist and offering local, mocked
     * packages, by writing repositories configuration to a directory. This
     * directory can be used as Composer's COMPOSER_HOME. By specifying this directory,
     * Composer will no longer depend on Internet connectivity and can safely be used
     * in integration and system tests.
     *
     * @param string $composerHomeDirectory
     */
    public static function mockRepositories($composerHomeDirectory)
    {
        $pathTo = function ($packageName) {
            return __DIR__ . '/../composer/packages/' . $packageName;
        };

        $configuration = [
            // Emulate all the tools' Composer packages locally to guarantee test
            // reliability by removing the Internet factor and to speed up tests.
            'repositories' => [
                ['packagist' => false],
                ['type' => 'path', 'url' => $pathTo('phpmd/phpmd')],
                ['type' => 'path', 'url' => $pathTo('squizlabs/php_codesniffer')],
                [
                    'type' => 'package',
                    'package' => [
                        'name' => 'drupal/coder',
                        'version' => '8.0',
                        'dist' => [
                            'url' => $pathTo('drupal/coder8'),
                            'type' => 'path'
                        ]
                    ]
                ],
                [
                    'type' => 'package',
                    'package' => [
                        'name' => 'drupal/coder',
                        'version' => '7.0',
                        'dist' => [
                            'url' => $pathTo('drupal/coder7'),
                            'type' => 'path'
                        ]
                    ]
                ],
                ['type' => 'path', 'url' => $pathTo('escapestudios/symfony2-coding-standard')],
            ],
        ];
        file_put_contents(
            $composerHomeDirectory . '/config.json',
            json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }
}
