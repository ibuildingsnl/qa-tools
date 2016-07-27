<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;
use Ibuildings\QaTools\Core\Task\Task;

final class TaskDirectory
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var TaskList
     */
    private $taskList;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->taskList = new TaskList([]);
    }

    /**
     * @param Task $task
     * @return TaskList
     */
    public function addTask(Task $task)
    {
        $this->taskList = $this->taskList->add($task);
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return TaskList
     */
    public function getTaskList()
    {
        return $this->taskList;
    }
}
