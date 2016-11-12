<?php

namespace Ibuildings\QaTools\SystemTest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\ProcessBuilder;

final class SystemTest extends TestCase
{
    /**
     * @test
     * @dataProvider specs
     */
    public function execute_specs($scriptPath, $specPath)
    {
        $projectDirectory = sys_get_temp_dir() . '/qa-tools_' . microtime(true) . '_system-test';
        mkdir($projectDirectory);

        switch (getenv('QA_TOOLS_BIN')) {
            case 'phar':
                symlink(__DIR__ . '/../../build/test/qa-tools.phar', $projectDirectory . '/qa-tools');
                break;
            default:
                symlink(__DIR__ . '/../../bin/qa-tools', $projectDirectory . '/qa-tools');
        }

        $expect = function () use ($scriptPath, $projectDirectory) {
            $this->expect(file_get_contents($scriptPath), $projectDirectory);
        };
        $spec = function () use ($expect, $specPath) {
            require $specPath;
        };

        $cwd = getcwd();
        chdir($projectDirectory);
        try {
            $spec();
        } finally {
            chdir($cwd);
        }
    }

    public function specs()
    {
        foreach (glob(__DIR__ . '/specs/*.php') as $specPath) {
            $specName = preg_replace('~\\.php$~', '', basename($specPath));
            $scriptPath = preg_replace('~\\.php$~', '.tcl', $specPath);
            yield $specName => [$scriptPath, $specPath];
        }
    }

    private function expect($script, $workingDirectory)
    {
        $scriptHarness = file_get_contents(__DIR__ . '/harness.tcl');
        $fullScript = str_replace('# SCRIPT #', $script, $scriptHarness);

        $process = ProcessBuilder::create(['expect'])
            ->setInput($fullScript)
            ->setWorkingDirectory($workingDirectory)
            ->setEnv('COMPOSER_HOME', getenv('COMPOSER_HOME'))
            ->getProcess();
        $process->run();

        if ($process->getExitCode() === 0) {
            return;
        }

        $expectStdout = preg_replace('~^~', '  ', $process->getOutput());
        $expectStderr = preg_replace('~^~', '  ', $process->getErrorOutput());
        $this->fail(
            sprintf(
                "QA Tools distributable terminated with non-zero exit code %d.\n\nCWD:\n  %s\nSTDOUT:\n%s\nSTDERR:\n%s",
                $process->getExitCode(),
                getcwd(),
                $expectStdout,
                $expectStderr
            )
        );
    }
}
