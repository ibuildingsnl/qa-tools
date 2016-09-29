<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

/**
 * Executes the tasks stored in the task directory.
 */
interface TaskDirectoryExecutor
{
    /**
     * @param TaskDirectory     $taskDirectory
     * @param ScopedInterviewer $interviewer
     * @return bool
     */
    public function execute(TaskDirectory $taskDirectory, ScopedInterviewer $interviewer);
}
