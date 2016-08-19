<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;

final class NoOpTask implements Task
{
    public function checkPrerequisites(Interviewer $interviewer)
    {
    }

    public function execute(Interviewer $interviewer)
    {
        return new NoOpTask();
    }

    public function equals(Task $task)
    {
        /** @var self $task */
        return get_class($this) === get_class($task);
    }
}
