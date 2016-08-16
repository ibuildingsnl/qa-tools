<?php

namespace Ibuildings\QaTools\Core\Task;

interface Specification
{
    /**
     * @param Task $task
     * @return bool
     */
    public function isSatisfiedBy(Task $task);
}
