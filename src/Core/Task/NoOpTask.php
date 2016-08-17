<?php

namespace Ibuildings\QaTools\Core\Task;

final class NoOpTask implements Task
{
    public function checkPrerequisites(Interviewer $interviewer)
    {
    }

    public function execute()
    {
        return new NoOpTask();
    }
}
