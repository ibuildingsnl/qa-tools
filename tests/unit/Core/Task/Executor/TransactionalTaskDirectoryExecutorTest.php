<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Configuration\InMemoryTaskDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Task\Executor\Executor;
use Ibuildings\QaTools\Core\Task\Executor\TransactionalTaskDirectoryExecutor;
use Ibuildings\QaTools\UnitTest\Core\Task\BarTask;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group TaskExecutor
 *
 * The Executor mocks in this test require arePrerequisitesMet, execute, and
 * cleanUp a specific amount of times, as per Executor's specification.
 *
 * @see Executor
 */
class TransactionalTaskDirectoryExecutorTest extends TestCase
{
    /** @test */
    public function executes_all_executors_properly()
    {
        /** @var MockInterface|Executor $executorA */
        $executorA = m::namedMock('ExecutorA', Executor::class);
        $executorA->shouldReceive('supports')->andReturn(true);
        $executorA->shouldReceive('arePrerequisitesMet')->atLeast()->once()->andReturn(true);
        $executorA->shouldReceive('execute')->once();
        $executorA->shouldReceive('cleanUp')->once();
        $executorA->shouldReceive('rollBack')->never();

        /** @var MockInterface|Executor $executorB */
        $executorB = m::namedMock('ExecutorB', Executor::class);
        $executorB->shouldReceive('supports')->andReturn(true);
        $executorB->shouldReceive('arePrerequisitesMet')->atLeast()->once()->andReturn(true);
        $executorB->shouldReceive('execute')->once();
        $executorB->shouldReceive('cleanUp')->once();
        $executorB->shouldReceive('rollBack')->never();

        $executors = [$executorA, $executorB];
        $transactionalExecutor = new TransactionalTaskDirectoryExecutor($executors);

        $taskDirectory = new InMemoryTaskDirectory(
            new Project('name', new Directory('.'), new Directory('.'), new ProjectTypeSet(), false)
        );
        $taskDirectory->registerTask(new BarTask());

        /** @var MockInterface|ScopedInterviewer $interviewer */
        $interviewer = m::spy(ScopedInterviewer::class);

        $transactionalExecutor->execute($taskDirectory, $interviewer);
    }

    /** @test */
    public function does_not_execute_any_executors_when_prerequisites_are_not_met()
    {
        /** @var MockInterface|Executor $executorWithMetPrerequisites */
        $executorWithMetPrerequisites = m::namedMock('ExecutorMet', Executor::class);
        $executorWithMetPrerequisites->shouldReceive('supports')->andReturn(true);
        $executorWithMetPrerequisites->shouldReceive('arePrerequisitesMet')->atLeast()->once()->andReturn(true);
        $executorWithMetPrerequisites->shouldNotReceive('execute');
        $executorWithMetPrerequisites->shouldNotReceive('cleanUp');
        $executorWithMetPrerequisites->shouldNotReceive('rollBack');

        /** @var MockInterface|Executor $executorWithUnmetPrerequisites */
        $executorWithUnmetPrerequisites = m::namedMock('ExecutorUnmet', Executor::class);
        $executorWithUnmetPrerequisites->shouldReceive('supports')->andReturn(true);
        $executorWithUnmetPrerequisites->shouldReceive('arePrerequisitesMet')->atLeast()->once()->andReturn(false);
        $executorWithUnmetPrerequisites->shouldNotReceive('execute');
        $executorWithUnmetPrerequisites->shouldNotReceive('cleanUp');
        $executorWithUnmetPrerequisites->shouldNotReceive('rollBack');

        $executors = [$executorWithMetPrerequisites, $executorWithUnmetPrerequisites];
        $transactionalExecutor = new TransactionalTaskDirectoryExecutor($executors);

        $taskDirectory = new InMemoryTaskDirectory(
            new Project('name', new Directory('.'), new Directory('.'), new ProjectTypeSet(), false)
        );
        $taskDirectory->registerTask(new BarTask());

        /** @var MockInterface|ScopedInterviewer $interviewer */
        $interviewer = m::spy(ScopedInterviewer::class);

        $transactionalExecutor->execute($taskDirectory, $interviewer);
    }
}
