<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Cli;

use Ibuildings\QaTools\Core\Cli\Cli;
use PHPUnit\Framework\TestCase;

final class CliTest extends TestCase
{
    /**
     * @test
     */
    public function detects_installed_executable()
    {
        $this->assertTrue(
            Cli::isExecutableInstalled('ls'),
            'Cli should have been able to detect that "ls" is installed on your system. Are you running windows?'
        );
    }

    /**
     * @test
     */
    public function detects_executable_not_installed()
    {
        $this->assertFalse(
            Cli::isExecutableInstalled('abcdefgzyxopjn'),
            'Cli was able to find a binary called "abcdefgzyxopjn", but expected not to.'
        );
    }
}
