<?php

namespace Ibuildings\QaTools\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Requirement\Requirement;

final class AnySpecification implements Specification
{
    public function isSatisfiedBy(Requirement $requirement)
    {
        return true;
    }

    public function equals(Specification $specification)
    {
        /** @var self $specification */
        return get_class($specification) === self::class;
    }

    public function __toString()
    {
        return sprintf('AnySpecification()');
    }
}
