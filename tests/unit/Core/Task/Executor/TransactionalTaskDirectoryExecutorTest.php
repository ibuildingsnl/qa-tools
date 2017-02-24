<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Exception as CoreException;
use Ibuildings\QaTools\Core\Configuration\InMemoryTaskDirectory;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Task\Executor\Executor;
use Ibuildings\QaTools\Core\Task\Executor\ExecutorCollection;
use Ibuildings\QaTools\Core\Task\Executor\TransactionalTaskDirectoryExecutor;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Ibuildings\QaTools\UnitTest\Core\Task\NoopTask;
use Mockery as m;
use Mockery\MockInterface;

/**
 * @group TaskExecutor
 *
 * The Executor mocks in this test require arePrerequisitesMet, execute, and
 * cleanUp a specific amount of times, as per Executor's specification.
 *
 * @see Executor
 */
class TransactionalTaskDirectoryExecutorTest extends MockeryTestCase
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

        $executors = m::mock(ExecutorCollection::class);
        $executors
            ->shouldReceive('findExecutorsWithAtLeastOneTaskToExecute')
            ->andReturn([$executorA, $executorB]);
        $transactionalExecutor = new TransactionalTaskDirectoryExecutor($executors);

        $taskDirectory = new InMemoryTaskDirectory(
            new Project('name', new Directory('.'), new Directory('.'), new ProjectTypeSet(), false)
        );
        $taskDirectory->registerTask(new NoopTask());

        /** @var MockInterface|ScopedInterviewer $interviewer */
        $interviewer = m::mock(ScopedInterviewer::class)->shouldIgnoreMissing();

        $this->assertTrue(
            $transactionalExecutor->execute($taskDirectory, $interviewer),
            'When execution succeeds, the transactional executor should return true, but it returns false'
        );
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

        $executors = m::mock(ExecutorCollection::class);
        $executors
            ->shouldReceive('findExecutorsWithAtLeastOneTaskToExecute')
            ->andReturn([$executorWithMetPrerequisites, $executorWithUnmetPrerequisites]);
        $transactionalExecutor = new TransactionalTaskDirectoryExecutor($executors);

        $taskDirectory = new InMemoryTaskDirectory(
            new Project('name', new Directory('.'), new Directory('.'), new ProjectTypeSet(), false)
        );
        $taskDirectory->registerTask(new NoopTask());

        /** @var MockInterface|ScopedInterviewer $interviewer */
        $interviewer = m::mock(ScopedInterviewer::class)->shouldIgnoreMissing();

        $this->assertFalse(
            $transactionalExecutor->execute($taskDirectory, $interviewer),
            'When an executor\'s prerequisite check fails, the transactional executor should return false, but it returns true'
        );
    }

    /** @test */
    public function does_not_execute_any_executors_when_a_prerequisites_check_throws_an_exception_and_bubbles_the_exception()
    {
        /** @var MockInterface|Executor $executorWithMetPrerequisites */
        $executorWithMetPrerequisites = m::namedMock('ExecutorMet', Executor::class);
        $executorWithMetPrerequisites->shouldReceive('supports')->andReturn(true);
        $executorWithMetPrerequisites->shouldReceive('arePrerequisitesMet')->atLeast()->once()->andReturn(true);
        $executorWithMetPrerequisites->shouldNotReceive('execute');
        $executorWithMetPrerequisites->shouldNotReceive('cleanUp');
        $executorWithMetPrerequisites->shouldNotReceive('rollBack');

        $prerequisitesException = new RuntimeException('Splines could not be reticulated');

        /** @var MockInterface|Executor $executorWithUnmetPrerequisites */
        $executorWithUnmetPrerequisites = m::namedMock('ExecutorUnmet', Executor::class);
        $executorWithUnmetPrerequisites->shouldReceive('supports')->andReturn(true);
        $executorWithUnmetPrerequisites->shouldReceive('arePrerequisitesMet')->atLeast()->once()->andThrow($prerequisitesException);
        $executorWithUnmetPrerequisites->shouldNotReceive('execute');
        $executorWithUnmetPrerequisites->shouldNotReceive('cleanUp');
        $executorWithUnmetPrerequisites->shouldNotReceive('rollBack');

        $executors = m::mock(ExecutorCollection::class);
        $executors
            ->shouldReceive('findExecutorsWithAtLeastOneTaskToExecute')
            ->andReturn([$executorWithMetPrerequisites, $executorWithUnmetPrerequisites]);
        $transactionalExecutor = new TransactionalTaskDirectoryExecutor($executors);

        $taskDirectory = new InMemoryTaskDirectory(
            new Project('name', new Directory('.'), new Directory('.'), new ProjectTypeSet(), false)
        );
        $taskDirectory->registerTask(new NoopTask());

        /** @var MockInterface|ScopedInterviewer $interviewer */
        $interviewer = m::mock(ScopedInterviewer::class)->shouldIgnoreMissing();

        try {
            $transactionalExecutor->execute($taskDirectory, $interviewer);
        } catch (CoreException $e) {
            $this->assertSame(
                $prerequisitesException,
                $e,
                'The transactional executor should let a prerequisites exception bubble'
            );
        }
    }
}
