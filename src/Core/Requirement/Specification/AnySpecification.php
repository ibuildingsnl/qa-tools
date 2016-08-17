<?php

namespace Ibuildings\QaTools\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Requirement\Requirement;

final class AnySpecification implements Specification
{
    public function isSatisfiedBy(Requirement $requirement)
    {
        return true;
    }

    public function __toString()
    {
        return sprintf('AnySpecification()');
    }
}
