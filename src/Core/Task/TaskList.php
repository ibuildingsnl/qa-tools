<?php

namespace Ibuildings\QaTools\Core\Task;

use ArrayIterator;
use Countable;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Task\Specification\Specification;
use IteratorAggregate;

final class TaskList implements IteratorAggregate, Countable
{
    /**
     * @var Task[]
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
     * @param Specification $specification
     * @return TaskList
     */
    public function match(Specification $specification)
    {
        return new TaskList(array_filter($this->tasks, [$specification, 'isSatisfiedBy']));
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

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->tasks) === 0;
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
