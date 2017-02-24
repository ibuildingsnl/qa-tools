<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Task\Executor\Sigints;
use Ibuildings\QaTools\Test\MockeryTestCase;

class SigintsTest extends MockeryTestCase
{
    protected function tearDown()
    {
        try {
            Sigints::resetTrap();
        } catch (RuntimeException $e) {
            // Ignore when the trap is not yet set or has already been set
        }
    }

    /** @test */
    public function receives_sigints()
    {
        Sigints::trap();

        posix_kill(posix_getpid(), SIGINT);

        $this->assertTrue(
            Sigints::wereTrapped(),
            'Sigints should report that the test process has wereTrapped a SIGINT'
        );
    }

    /** @test */
    public function clears_any_previously_received_signal_when_checking()
    {
        Sigints::trap();

        posix_kill(posix_getpid(), SIGINT);
        Sigints::wereTrapped();

        $this->assertTrue(
            Sigints::wereTrapped(),
            'Sigints should still report having wereTrapped a signal previously'
        );
    }

    /** @test */
    public function reports_not_having_received_a_sigint()
    {
        Sigints::trap();

        $this->assertFalse(
            Sigints::wereTrapped(),
            'Sigints should not report that the test process has wereTrapped any SIGINT signal'
        );
    }

    /** @test */
    public function cannot_be_registered_twice()
    {
        Sigints::trap();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('already registered');
        Sigints::trap();
    }

    /** @test */
    public function cannot_be_deregistered_twice()
    {
        Sigints::trap();
        Sigints::resetTrap();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('has not been registered');
        Sigints::resetTrap();
    }

    /** @test */
    public function reports_not_having_received_a_sigint_after_reregistration()
    {
        Sigints::trap();
        posix_kill(posix_getpid(), SIGINT);
        Sigints::resetTrap();
        Sigints::trap();

        $this->assertFalse(
            Sigints::wereTrapped(),
            'Sigints should not report that the test process has wereTrapped any SIGINT signal since reregistration'
        );
    }
}
