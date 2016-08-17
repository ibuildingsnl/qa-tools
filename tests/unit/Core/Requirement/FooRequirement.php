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

    /**
     * @param Requirement $other
     * @return bool
     */
    public function equals(Requirement $other)
    {
        return get_class($other) === self::class
            && $this->testValue === $other->testValue;
    }
}
