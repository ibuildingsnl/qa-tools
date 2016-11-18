<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Countable;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use IteratorAggregate;

interface ExecutorCollection extends Countable, IteratorAggregate
{
    /**
     * @param TaskDirectory $taskDirectory
     * @return ExecutorCollection
     */
    public function findExecutorsWithAtLeastOneTaskToExecute(TaskDirectory $taskDirectory);
}
