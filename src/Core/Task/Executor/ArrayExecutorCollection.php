<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use ArrayIterator;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;

final class ArrayExecutorCollection implements ExecutorCollection
{
    /** @var Executor[] */
    private $executors;

    /**
     * @param Executor[] $executors
     */
    public function __construct(array $executors)
    {
        Assertion::allIsInstanceOf(
            $executors,
            Executor::class,
            'Executors ought to be instances of Executor, got an instance of "%s"'
        );

        $this->executors = $executors;
    }

    public function findExecutorsWithAtLeastOneTaskToExecute(TaskDirectory $taskDirectory)
    {
        return new ArrayExecutorCollection(
            array_filter(
                $this->executors,
                function (Executor $executor) use ($taskDirectory) {
                    return count($taskDirectory->filterTasks([$executor, 'supports'])) > 0;
                }
            )
        );
    }

    public function count()
    {
        return count($this->executors);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->executors);
    }
}
