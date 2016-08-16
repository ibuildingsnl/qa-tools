<?php

namespace Ibuildings\QaTools\Core\Task\Specification;

use Ibuildings\QaTools\Core\Task\Task;

final class AnySpecification implements Specification
{
    public function isSatisfiedBy(Task $task)
    {
        return true;
    }

    public function __toString()
    {
        return sprintf('AnySpecification()');
    }
}
