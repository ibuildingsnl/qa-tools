<?php

namespace Ibuildings\QaTools\Core\Requirement\Specification;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Requirement\Requirement;

final class TypeSpecification implements Specification
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        Assertion::string($type, 'The type ought to be a string, got "%s" of type "%s"');

        $this->type = $type;
    }

    public function isSatisfiedBy(Requirement $requirement)
    {
        return get_class($requirement) === $this->type;
    }

    public function __toString()
    {
        return sprintf('TypeSpecification("%s")', $this->type);
    }
}
