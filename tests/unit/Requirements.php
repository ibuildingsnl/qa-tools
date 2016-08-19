<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\Specification\Specification;
use Mockery;

final class Requirements
{
    /**
     * @param Specification $specification
     * @return Mockery\Matcher\Closure
     */
    public static function requirementMatching(Specification $specification)
    {
        return Mockery::on(
            function (Requirement $requirement) use ($specification) {
                return $specification->isSatisfiedBy($requirement);
            }
        );
    }
}
