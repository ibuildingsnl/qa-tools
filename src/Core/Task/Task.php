<?php

namespace Ibuildings\QaTools\Core\Task;

interface Task
{
    /**
     * @param Task $other
     * @return boolean
     */
    public function equals(Task $other);
}
