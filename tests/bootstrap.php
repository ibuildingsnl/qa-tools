<?php

use Ibuildings\QaTools\ComposerTest\Composer;

require __DIR__ . '/../vendor/autoload.php';

// Don't introduce globals by wrapping assignments in immediately-invoked function.
call_user_func(function () {
    $composerHomeDirectory = __DIR__ . '/../var/composer';
    if (!file_exists($composerHomeDirectory)) {
        mkdir($composerHomeDirectory);
    }

    Composer::mockRepositories($composerHomeDirectory);

    // Publish Composer home directory as an environment variable. Setting this does
    // not mean it is automatically passed to child processes.
    putenv(sprintf('COMPOSER_HOME=%s', $composerHomeDirectory));
});
