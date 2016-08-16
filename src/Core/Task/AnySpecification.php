<?php

namespace Ibuildings\QaTools\Core\Task;

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
