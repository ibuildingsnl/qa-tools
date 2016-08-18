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
}
