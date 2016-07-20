<?php

namespace QaTools\SmokeTest;

use PHPUnit\Framework\TestCase;

final class IsExecutableTest extends TestCase
{
    /** @test */
    public function the_distributable_is_executable()
    {
        exec('dist/qa-tools.phar list', $outputLines, $exitCode);
        $output = join("\n", $outputLines);

        $this->assertSame(0, $exitCode);
        $this->assertContains('Available commands:', $output);
    }
}
