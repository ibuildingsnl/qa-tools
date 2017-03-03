<?php

namespace Ibuildings\QaTools\SystemTest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group system
 */
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

        $expect = function ($chapter = null) use ($scriptPath, $projectDirectory) {
            if ($chapter) {
                $scriptPath = preg_replace('~\.tcl$~', sprintf('_%s.tcl', $chapter), $scriptPath);
            }
            $this->expect(file_get_contents($scriptPath), $projectDirectory);
        };
        $spec = function () use ($expect, $specPath) {
            require $specPath;
        };

        $cwd = getcwd();
        chdir($projectDirectory);
        try {
            $spec();

            (new Filesystem())->remove($projectDirectory);
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

        $message = <<<MESSAGE
▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒

QA Tools distributable terminated with non-zero exit code %d.

Possible causes:
  1. QA Tools did not output the expected text within a certain amount of time. Check the output of QA Tools below.
  2. A Composer package could not be installed.
     Maybe you forgot to:
       - Add a composer.json fixture for the package to 'tests/composer/packages'
       - Add the package to '\\Ibuildings\\QaTools\\SystemTest\\Composer::initialise'
     Composer packages are mocked. Please read the documentation on why, and how to mock yours: docs/development/writing-system-tests.md.

CWD:
%s

STDOUT:
%s

STDERR:
%s

▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒

MESSAGE;

        $this->fail(
            sprintf(
                $message,
                $process->getExitCode(),
                getcwd(),
                $expectStdout,
                $expectStderr
            )
        );
    }
}
