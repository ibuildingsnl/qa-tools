<?php

namespace Ibuildings\QaTools\Core\Task\Execution;

use Ibuildings\QaTools\Core\Configuration\MemorizingInterviewer;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Execution\TaskDirectoryExecutor;

/**
 * Doesn't execute any of the tasks in the task directory.
 */
final class NoOpTaskDirectoryExecutor implements TaskDirectoryExecutor
{
    public function execute(TaskDirectory $taskDirectory, MemorizingInterviewer $interviewer)
    {
    }
}
