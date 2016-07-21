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
        $projectDirectory = sys_get_temp_dir() . '/' . uniqid('qa-tools_system', true);
        mkdir($projectDirectory);
        link(__DIR__ . '/../../dist/qa-tools.phar', $projectDirectory . '/qa-tools');
        link(__DIR__ . '/../../dist/qa-tools.phar.pubkey', $projectDirectory . '/qa-tools.pubkey');

        $this->expect(file_get_contents($scriptPath), $projectDirectory);

        $cwd = getcwd();
        chdir($projectDirectory);
        try {
            require $specPath;
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
            ->getProcess();
        $process->run();

        if ($process->getExitCode() === 0) {
            return;
        }

        $expectStdout = preg_replace('~^~', '  ', $process->getOutput());
        $expectStderr = preg_replace('~^~', '  ', $process->getErrorOutput());
        $this->fail(
            sprintf(
                "QA Tools distributable behaved in an unexpected manner.\n\nSTDOUT:\n%s\nSTDERR:\n%s",
                $expectStdout,
                $expectStderr
            )
        );
    }
}
