<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Ibuildings\QaTools\Core\Task\Task;

final class FooTask implements Task
{
    private $testValue;

    public function __construct($testValue)
    {
        $this->testValue = $testValue;
    }
}
