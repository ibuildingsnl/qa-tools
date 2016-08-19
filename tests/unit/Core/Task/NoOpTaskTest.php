<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\NoOpTask;
use Ibuildings\QaTools\UnitTest\Diffing;
use Mockery;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Test
 */
class NoOpTaskTest extends TestCase
{
    use Diffing;

    /** @test */
    public function can_check_its_prerequisites()
    {
        (new NoOpTask())->checkPrerequisites(Mockery::mock(Interviewer::class));
    }

    /** @test */
    public function can_execute_its_task()
    {
        (new NoOpTask())->execute(Mockery::mock(Interviewer::class));
    }

    /** @test */
    public function equals_another_noop_task()
    {
        $a = new NoOpTask();
        $b = new NoOpTask();

        $this->assertTrue($a->equals($b), $this->diff($a, $b, 'Two NoOpTasks should equal each other'));
    }
}
