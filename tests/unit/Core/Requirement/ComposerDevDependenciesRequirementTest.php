<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Requirement\ComposerDevDependenciesRequirement;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Requirement
 */
class ComposerDevDependenciesRequirementTest extends TestCase
{
    /**
     * @test
     * @dataProvider equalRequirements
     * @param ComposerDevDependenciesRequirement $a
     * @param ComposerDevDependenciesRequirement $b
     */
    public function can_be_equal(ComposerDevDependenciesRequirement $a, ComposerDevDependenciesRequirement $b)
    {
        $this->assertTrue($a->equals($b), 'Requirements ought to equal each other');
    }

    public function equalRequirements()
    {
        return [
            '[c/c:dev-master] == [c/c:dev-master]' => [new ComposerDevDependenciesRequirement(Package::of('c/c', 'dev-master')), new ComposerDevDependenciesRequirement(Package::of('c/c', 'dev-master'))],
            '[a/a:1, b/b:2] == [b/b:2, a/a:1]' => [new ComposerDevDependenciesRequirement(Package::of('a/a', '1'), Package::of('b/b', '2')), new ComposerDevDependenciesRequirement(Package::of('b/b', '2'), Package::of('a/a', '1'))],
        ];
    }

    /**
     * @test
     * @dataProvider unequalRequirements
     * @param ComposerDevDependenciesRequirement $a
     * @param ComposerDevDependenciesRequirement $b
     */
    public function can_not_be_equal(ComposerDevDependenciesRequirement $a, ComposerDevDependenciesRequirement $b)
    {
        $this->assertFalse($a->equals($b), 'Requirements ought not to equal each other');
    }

    public function unequalRequirements()
    {
        return [
            '[c/c:dev-master] != [a/a:1]' => [new ComposerDevDependenciesRequirement(Package::of('c/c', 'dev-master')), new ComposerDevDependenciesRequirement(Package::of('a/a', '1'))],
            '[a/a:1] != [b/b:2]' => [new ComposerDevDependenciesRequirement(Package::of('a/a', '1')), new ComposerDevDependenciesRequirement(Package::of('b/b', '2'))],
        ];
    }
}
