<?php

namespace Ibuildings\QaTools\Core\Task\Execution;

use Ibuildings\QaTools\Core\Configuration\MemorizingInterviewer;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;

interface TaskDirectoryExecutor
{
    /**
     * @param TaskDirectory         $taskDirectory
     * @param MemorizingInterviewer $interviewer
     * @return void
     */
    public function execute(TaskDirectory $taskDirectory, MemorizingInterviewer $interviewer);
}
