<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

final class InMemoryTaskDirectory implements TaskDirectory
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var TaskList
     */
    private $tasks;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->tasks = new TaskList();
    }

    public function registerTask(Task $task)
    {
        $this->tasks = $this->tasks->add($task);
    }

    /**
     * @param callable $predicate
     * @return TaskList
     */
    public function filterTasks(callable $predicate)
    {
        return $this->tasks->filter($predicate);
    }

    /**
     * @return TaskList
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function getProject()
    {
        return $this->project;
    }
}
