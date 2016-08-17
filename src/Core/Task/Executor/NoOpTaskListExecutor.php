<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Task\TaskList;

final class NoOpTaskListExecutor implements TaskListExecutor
{
    public function execute(TaskList $tasks, ScopedInterviewer $interviewer)
    {
    }
}
