<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Specification\Specification;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

interface TaskDirectory
{
    /**
     * @param Task   $task
     * @param string $toolClassName
     * @return void
     */
    public function registerTask(Task $task, $toolClassName);

    /**
     * @param Specification $taskSpecification
     * @return TaskList
     */
    public function matchTasks(Specification $taskSpecification);

    /**
     * @return Project
     */
    public function getProject();
}
