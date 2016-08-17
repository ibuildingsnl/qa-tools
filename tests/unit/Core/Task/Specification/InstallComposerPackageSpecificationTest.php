<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Specification;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Task\InstallComposerPackagesTask;
use Ibuildings\QaTools\Core\Task\Specification\InstallComposerPackageSpecification;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 */
class InstallComposerPackageSpecificationTest extends TestCase
{
    /** @test */
    public function phpmd_ought_to_satisfy_a_phpmd_package_specification()
    {
        $specification = InstallComposerPackageSpecification::ofAnyVersion(new PackageName('phpmd/phpmd'));
        $this->assertTrue(
            $specification->isSatisfiedBy(
                new InstallComposerPackagesTask(new PackageSet([Package::of('phpmd/phpmd', '3.1.1')]))
            ),
            'Specification for installation of any version of PHPMD ought to be satisfied ' .
            'by task to install phpmd/phpmd:3.1.1'
        );
    }
}
