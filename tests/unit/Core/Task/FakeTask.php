<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Ibuildings\QaTools\Core\Task\Task;

final class FakeTask implements Task
{
    private $testValue;

    public function __construct($testValue)
    {
        $this->testValue = $testValue;
    }

    /**
     * @param Task $other
     * @return bool
     */
    public function equals(Task $other)
    {
        return $this->testValue === $other->testValue;
    }
}
