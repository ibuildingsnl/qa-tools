<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Task\TaskList;

interface TaskListExecutor
{
    /**
     * Executes all tasks in the list, rolling back all executed tasks in reverse
     * order when a task fails.
     *
     * @param TaskList          $tasks
     * @param ScopedInterviewer $interviewer
     * @return void
     */
    public function execute(TaskList $tasks, ScopedInterviewer $interviewer);
}
