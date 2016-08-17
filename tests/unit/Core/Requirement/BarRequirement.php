<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement;

use Ibuildings\QaTools\Core\Requirement\Requirement;

final class BarRequirement implements Requirement
{
   /**
     * @param Requirement $other
     * @return bool
     */
    public function equals(Requirement $other)
    {
        return get_class($other) === self::class;
    }
}
