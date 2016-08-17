<?php

namespace Ibuildings\QaTools\Core\Task\Execution;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;

interface TaskDirectoryExecutor
{
    /**
     * @param TaskDirectory     $taskDirectory
     * @param ScopedInterviewer $interviewer
     * @return void
     */
    public function execute(TaskDirectory $taskDirectory, ScopedInterviewer $interviewer);
}
