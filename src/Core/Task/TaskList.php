<?php

namespace Ibuildings\QaTools\Core\Task;

use ArrayIterator;
use Countable;
use Ibuildings\QaTools\Core\Assert\Assertion;
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
     * @param Task $task
     * @return TaskList
     */
    public function add(Task $task)
    {
        return new TaskList(array_merge($this->tasks, [$task]));
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

    /**
     * @param TaskList $other
     * @return bool
     */
    public function equals(TaskList $other)
    {
        if (count($this->tasks) !== count($other->tasks)) {
            return false;
        }

        foreach ($this->tasks as $i => $task) {
            if (!$other->tasks[$i]->equals($task)) {
                return false;
            }
        }

        return true;
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
