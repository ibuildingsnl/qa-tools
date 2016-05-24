<?php

namespace Ibuildings\QaTools\Core\Task\Runner;

use ArrayIterator;
use Countable;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Task\Task;
use IteratorAggregate;

final class TaskList implements IteratorAggregate, Countable
{
    /**
     * @var Task[] $tasks
     */
    private $tasks;

    /**
     * @param Task[] $tasks
     */
    public function __construct(array $tasks)
    {
        Assertion::allIsInstanceOf($tasks, Task::class);

        $this->tasks = $tasks;
    }

    /**
     * @param callable $predicate
     * @return TaskList
     */
    public function filter(callable $predicate)
    {
        return new self(array_filter($this->tasks, $predicate));
    }

    /**
     * @param Task $taskToBeFound
     * @return boolean
     */
    public function contains(Task $taskToBeFound)
    {
        /** @var Task $task */
        foreach ($this->tasks as $task) {
            if ($task->equals($taskToBeFound)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param TaskList $other
     * @return TaskList
     */
    public function merge(TaskList $other)
    {
        return new TaskList(
            array_merge(
                $this->tasks,
                array_filter(
                    $other->tasks,
                    function (Task $task) {
                        return !$this->contains($task);
                    }
                )
            )
        );
    }

    public function count()
    {
        return count($this->tasks);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->tasks);
    }
}
