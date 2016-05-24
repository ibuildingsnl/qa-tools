<?php

namespace Ibuildings\QaTools\Core\ConfigurationBuilder;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Runner\TaskList;

final class ConfigurationBuilder
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
    }

    /**
     * @param TaskList $taskList
     * @return TaskList
     */
    public function addTasks(TaskList $taskList)
    {
        return $this->taskList->merge($taskList);
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }
}
