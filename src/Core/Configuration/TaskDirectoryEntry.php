<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Task\Specification;
use Ibuildings\QaTools\Core\Task\Task;

final class TaskDirectoryEntry
{
    /**
     * @var Task
     */
    private $task;

    /**
     * @var string
     */
    private $registeredByTool;

    /**
     * @param Task   $task
     * @param string $registeredByTool
     */
    public function __construct(Task $task, $registeredByTool)
    {
        Assertion::string($registeredByTool, 'Tool ought to be a tool class name, got "%s" of type "%s"');

        $this->task = $task;
        $this->registeredByTool = $registeredByTool;
    }

    /**
     * @param Specification $specification
     * @return bool
     */
    public function taskSatisfies(Specification $specification)
    {
        return $specification->isSatisfiedBy($this->task);
    }

    /**
     * @param string $toolClassName
     * @return bool
     */
    public function wasRegisteredByTool($toolClassName)
    {
        Assertion::string($toolClassName, 'Tool class name ought to be a string, got "%s" of type "%s"');

        return $this->registeredByTool === $toolClassName;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    public function __toString()
    {
        return sprintf('TaskDirectoryEntry(task=%s, tool=%s)');
    }
}
