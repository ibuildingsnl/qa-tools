<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Specification;

use Ibuildings\QaTools\Core\Composer\Project;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependenciesTask;
use Ibuildings\QaTools\Core\Task\Specification\InstallComposerPackageSpecification;
use Mockery;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 */
class InstallComposerPackageSpecificationTest extends TestCase
{
    /** @test */
    public function phpmd_ought_to_satisfy_a_phpmd_package_specification()
    {
        $composer = Mockery::mock(Project::class);

        $specification = InstallComposerPackageSpecification::ofAnyVersion(new PackageName('phpmd/phpmd'));
        $this->assertTrue(
            $specification->isSatisfiedBy(
                new InstallComposerDevDependenciesTask(new PackageSet([Package::of('phpmd/phpmd', '3.1.1')]), $composer)
            ),
            'Specification for installation of any version of PHPMD ought to be satisfied ' .
            'by task to install phpmd/phpmd:3.1.1'
        );
    }
}
