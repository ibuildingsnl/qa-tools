<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class PackageName
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        Assertion::string($name, 'Package name ought to be a string, got "%s" of type "%s"');
        Assertion::regex(
            $name,
            '{^[A-Za-z0-9_./-]+$}',
            'Package name "%s" is invalid, it may only consist of [A-Za-z0-9_./-]'
        );

        $this->name = $name;
    }

    /**
     * @param PackageName $other
     * @return bool
     */
    public function equals(PackageName $other)
    {
        return $this->name === $other->name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return sprintf('PackageName("%s")', $this->name);
    }
}
