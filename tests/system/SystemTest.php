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
        $projectDirectory = sys_get_temp_dir() . '/' . uniqid('qa-tools_smoke', true);
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
            $scriptPath = preg_replace('~\\.php$~', '.expect', $specPath);
            yield $specName => [$scriptPath, $specPath];
        }
    }

    private function expect($script, $workingDirectory)
    {
        $header = <<<'HEADER'
set timeout 1

proc timed_out { expected } {
    puts "Expected string \"$expected\" did not appear in time. It is likely another question is pending. Aborting..."
    exit 1
}
proc early_eof { expected } {
    puts "Command terminated early while waiting for \"$expected\"."
    exit 1
}
proc should_see { expected } {
    expect {
        $expected {}
        timeout { timed_out $expected }
        eof { early_eof $expected }
    }
}
proc answer { expected with answer } {
    expect {
        $expected { send "$answer\n" }
        timeout { timed_out $expected }
        eof { early_eof $expected }
    }
}
proc expect_eof {} {
    puts "Waiting for command to terminate..."
    expect {
        eof     { puts "Test completed." }
        timeout {
            puts "Command failed to terminate in time. Perhaps there's still a question pending?"
            exit 1
        }
    }
}
HEADER;

        $process = ProcessBuilder::create(['expect'])
            ->setInput("$header\n\n$script")
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
