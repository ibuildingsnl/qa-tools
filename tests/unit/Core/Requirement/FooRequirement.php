<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement;

use Ibuildings\QaTools\Core\Requirement\Requirement;

final class FooRequirement implements Requirement
{
    private $testValue;

    public function __construct($testValue)
    {
        $this->testValue = $testValue;
    }
}
