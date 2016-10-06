<?php

namespace Ibuildings\QaTools\SystemTest;

use Composer\Json\JsonManipulator;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

final class Composer
{
    /**
     * Initialise a new Composer project.
     */
    public static function initialise()
    {
        $url = function ($packageName) {
            return __DIR__ . '/../composer/packages/'. $packageName;
        };

        $configuration = [
            // Emulate all the tools' Composer packages locally to guarantee test
            // reliability by removing the Internet factor and to speed up tests.
            'repositories' => [
                ['packagist' => false],
                ['type' => 'path', 'url' => $url('phpmd/phpmd')],
                ['type' => 'path', 'url' => $url('squizlabs/php_codesniffer')],
                [
                    'type' => 'package',
                    'package' => [
                        'name' => 'drupal/coder',
                        'version' => '8.0',
                        'dist' => [
                            'url' => $url('drupal/coder8'),
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
                            'url' => $url('drupal/coder7'),
                            'type' => 'path'
                        ]
                    ]
                ],
                ['type' => 'path', 'url' => $url('escapestudios/symfony2-coding-standard')],
            ],
        ];
        file_put_contents('composer.json', json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Adds a dependency conflict.
     *
     * @param string $packageName
     * @param string $packageVersionConstraint
     * @return void
     */
    public static function addConflict($packageName, $packageVersionConstraint)
    {
        $manipulator = new JsonManipulator(file_get_contents('composer.json'));
        $manipulator->addSubNode('conflict', $packageName, $packageVersionConstraint);

        file_put_contents('composer.json', $manipulator->getContents());
    }

    /**
     * Performs a `composer install` in the current working directory.
     *
     * @return void
     */
    public static function install()
    {
        $process = ProcessBuilder::create(['composer', 'install'])->getProcess();

        if ($process->run() !== 0) {
            throw new RuntimeException(sprintf('Composer install failed: %s', $process->getErrorOutput()));
        }
    }

    /**
     * @param string $packageName
     */
    public static function assertPackageIsInstalled($packageName)
    {
        assertFileExists(sprintf('vendor/%s/composer.json', $packageName));
    }

    /**
     * @param string $packageName
     */
    public static function assertPackageIsNotInstalled($packageName)
    {
        assertFileNotExists(sprintf('vendor/%s', $packageName));
    }
}
