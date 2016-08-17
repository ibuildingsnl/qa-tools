<?php

namespace Ibuildings\QaTools\Core\Requirement;

interface Requirement
{
    /**
     * @param Requirement $other
     * @return boolean
     */
    public function equals(Requirement $other);
}
