<?php

namespace Ibuildings\QaTools\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Requirement\Requirement;

interface Specification
{
    /**
     * @param Requirement $requirement
     * @return bool
     */
    public function isSatisfiedBy(Requirement $requirement);
}
