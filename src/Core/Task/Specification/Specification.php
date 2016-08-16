<?php

namespace Ibuildings\QaTools\Core\Task\Specification;

use Ibuildings\QaTools\Core\Task\Task;

interface Specification
{
    /**
     * @param Task $task
     * @return bool
     */
    public function isSatisfiedBy(Task $task);
}
