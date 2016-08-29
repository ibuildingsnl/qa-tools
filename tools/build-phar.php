#!/usr/bin/env php
<?php

info("Installing production dependencies for inclusion in the distributable...");
if (!execute('composer install --ansi --no-dev --optimize-autoloader 2>&1', $installOutput)) {
    error("Something went wrong while installing the production dependencies:");
    debug($installOutput);
    exit(1);
}

// Increase open file limit
// See https://github.com/box-project/box2/issues/80#issuecomment-76630852
execute('command -v ulimit 2>/dev/null', $ulimit);
if ($ulimit) {
    info("Building distributable with increased file descriptor limit...");
    $buildSucceeded = execute("$ulimit -Sn 4096 && composer build --ansi 2>&1", $buildOutput);
} else {
    $buildSucceeded = execute("composer build --ansi 2>&1", $buildOutput);
}

if (!$buildSucceeded) {
    error("Something went wrong while building the distributable:");
    debug($buildOutput);
}

info("Restoring development dependencies...");
if (!execute("composer install --ansi 2>&1", $installOutput)) {
    error("Something went wrong while restoring the development dependencies:");
    debug($installOutput);
}

exit($buildSucceeded ? 0 : 1);

function debug($message) {
    fprintf(STDERR, "%s\n", $message);
}
function info($message) {
    fprintf(STDERR, "%s%s%s\n", "\033[33m", $message, "\033[0m");
}
function error($message) {
    fprintf(STDERR, "%s%s%s\n", "\033[41m", $message, "\033[0m");
}

/**
 * @param string $command
 * @param string &$stdout
 * @return bool Whether the process exited with exit code 0
 */
function execute($command, &$stdout) {
    exec($command, $output, $exitCode);
    $stdout = join("\n", $output);

    return $exitCode === 0;
}
