<?php

namespace Ibuildings\QaTools\Core\Composer;

final class Package
{
    /**
     * @var PackageName
     */
    private $name;

    /**
     * @var PackageVersionConstraint
     */
    private $versionConstraint;

    /**
     * @param string $name
     * @param string $versionConstraint
     * @return Package
     */
    public static function of($name, $versionConstraint)
    {
        return new Package(new PackageName($name), PackageVersionConstraint::parse($versionConstraint));
    }

    /**
     * @param PackageName $name
     * @param PackageVersionConstraint $versionConstraint
     */
    public function __construct(PackageName $name, PackageVersionConstraint $versionConstraint)
    {
        $this->name = $name;
        $this->versionConstraint = $versionConstraint;
    }

    /**
     * @param PackageVersionConstraint $versionConstraint
     * @return bool
     */
    public function versionConstraintEquals(PackageVersionConstraint $versionConstraint)
    {
        return $this->versionConstraint->equals($versionConstraint);
    }

    /**
     * @param Package $other
     * @return bool
     */
    public function equals(Package $other)
    {
        return $this->name->equals($other->name)
            && $this->versionConstraint->equals($other->versionConstraint);
    }

    /**
     * Returns this package's descriptor, eg. "phpmd/phpmd:^2.0".
     *
     * @return string
     */
    public function getDescriptor()
    {
        return sprintf(
            '%s:%s',
            $this->getName()->getName(),
            $this->getVersionConstraint()->getConstraint()
        );
    }

    /**
     * @return PackageName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return PackageVersionConstraint
     */
    public function getVersionConstraint()
    {
        return $this->versionConstraint;
    }

    public function __toString()
    {
        return sprintf('Package("%s:%s")', $this->name->getName(), $this->versionConstraint->getConstraint());
    }
}
