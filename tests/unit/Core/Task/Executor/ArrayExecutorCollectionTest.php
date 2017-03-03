<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Executor;

use Ibuildings\QaTools\Core\Configuration\InMemoryTaskDirectory;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use Ibuildings\QaTools\Core\Task\Executor\ArrayExecutorCollection;
use Ibuildings\QaTools\Core\Task\Executor\Executor;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Ibuildings\QaTools\UnitTest\Core\Task\NoopTask;
use Mockery as m;
use Mockery\MockInterface;

/**
 * @group TaskExecutor
 */
class ArrayExecutorCollectionTest extends MockeryTestCase
{
    /** @var MockInterface|Executor */
    private $supportingExecutorA;
    /** @var MockInterface|Executor */
    private $supportingExecutorB;
    /** @var MockInterface|Executor */
    private $notSupportingExecutor;

    /**
     * @test
     * @dataProvider executors
     * @param Executor[] $allExecutors
     * @param Executor[] $expectedExecutors
     */
    public function finds_executors_that_have_at_least_one_task_to_execute($allExecutors, $expectedExecutors)
    {
        $project = new Project('name', new Directory('.'), new Directory('.'), new ProjectTypeSet(), false);
        $taskDirectory = new InMemoryTaskDirectory($project);
        $taskDirectory->registerTask(new NoopTask());

        $allExecutorsCollection = new ArrayExecutorCollection($allExecutors);
        $actualExecutorsCollection = $allExecutorsCollection->findExecutorsWithAtLeastOneTaskToExecute($taskDirectory);

        $this->assertSame($expectedExecutors, iterator_to_array($actualExecutorsCollection));
    }

    public function executors()
    {
        $this->supportingExecutorA = m::mock(Executor::class);
        $this->supportingExecutorA->shouldReceive('supports')->andReturn(true);

        $this->supportingExecutorB = m::mock(Executor::class);
        $this->supportingExecutorB->shouldReceive('supports')->andReturn(true);

        $this->notSupportingExecutor = m::mock(Executor::class);
        $this->notSupportingExecutor->shouldReceive('supports')->andReturn(false);

        return [
            'no executors' => [
                [],
                []
            ],
            'two supporting executors' => [
                [$this->supportingExecutorA, $this->supportingExecutorB],
                [$this->supportingExecutorA, $this->supportingExecutorB],
            ],
            'one of two executors support a task' => [
                [$this->supportingExecutorA, $this->notSupportingExecutor],
                [$this->supportingExecutorA],
            ],
        ];
    }
}
