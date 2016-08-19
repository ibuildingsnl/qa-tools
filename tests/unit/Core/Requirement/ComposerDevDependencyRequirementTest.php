<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Requirement\ComposerDevDependencyRequirement;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Requirement
 */
class ComposerDevDependencyRequirementTest extends TestCase
{
    /**
     * @test
     * @dataProvider equalRequirements
     * @param ComposerDevDependencyRequirement $a
     * @param ComposerDevDependencyRequirement $b
     */
    public function can_be_equal(ComposerDevDependencyRequirement $a, ComposerDevDependencyRequirement $b)
    {
        $this->assertTrue($a->equals($b), 'Requirements ought to equal each other');
    }

    public function equalRequirements()
    {
        return [
            '[c/c:dev-master] == [c/c:dev-master]' => [new ComposerDevDependencyRequirement(Package::of('c/c', 'dev-master')), new ComposerDevDependencyRequirement(Package::of('c/c', 'dev-master'))],
            '[a/a:1] == [a/a:1.0]' => [new ComposerDevDependencyRequirement(Package::of('a/a', '1')), new ComposerDevDependencyRequirement(Package::of('a/a', '1.0'))],
        ];
    }

    /**
     * @test
     * @dataProvider unequalRequirements
     * @param ComposerDevDependencyRequirement $a
     * @param ComposerDevDependencyRequirement $b
     */
    public function can_not_be_equal(ComposerDevDependencyRequirement $a, ComposerDevDependencyRequirement $b)
    {
        $this->assertFalse($a->equals($b), 'Requirements ought not to equal each other');
    }

    public function unequalRequirements()
    {
        return [
            '[c/c:dev-master] != [a/a:1]' => [new ComposerDevDependencyRequirement(Package::of('c/c', 'dev-master')), new ComposerDevDependencyRequirement(Package::of('a/a', '1'))],
            '[a/a:1] != [b/b:2]' => [new ComposerDevDependencyRequirement(Package::of('a/a', '1')), new ComposerDevDependencyRequirement(Package::of('b/b', '2'))],
        ];
    }
}
