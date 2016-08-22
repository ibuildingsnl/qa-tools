<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

interface TaskDirectory
{
    /**
     * @param Task $task
     * @return void
     */
    public function registerTask(Task $task);

    /**
     * @param callable $predicate
     * @return TaskList
     */
    public function filterTasks(callable $predicate);

    /**
     * @return Project
     */
    public function getProject();
}
