<?php

namespace Ibuildings\QaTools\Core\Task\Specification;

use Ibuildings\QaTools\Core\Task\Task;

final class EqualsSpecification implements Specification
{
    /**
     * @var Task
     */
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function isSatisfiedBy(Task $task)
    {
        return $task->equals($this->task);
    }

    public function __toString()
    {
        return sprintf('EqualsSpecification(%s)', $this->task);
    }
}
