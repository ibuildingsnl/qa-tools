<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Specification;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

final class InMemoryTaskDirectory implements TaskDirectory
{
    /**
     * @var Project
     */
    private $project;

    /**
     * @var TaskDirectoryEntry[]
     */
    private $entries;

    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->entries = [];
    }

    public function registerTask(Task $task, $toolClassName)
    {
        $this->entries[] = new TaskDirectoryEntry($task, $toolClassName);
    }

    public function matchTasks(Specification $specification)
    {
        $matchingTasks = [];
        foreach ($this->entries as $entry) {
            if ($entry->taskSatisfies($specification)) {
                $matchingTasks[] = $entry->getTask();
            }
        }

        return new TaskList($matchingTasks);
    }

    public function getProject()
    {
        return $this->project;
    }
}
