<?php

namespace Ibuildings\QaTools\UnitTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\PackageVersionConstraint;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Ibuildings\QaTools\UnitTest\Diffing;

/**
 * @group Composer
 */
class PackageVersionConstraintTest extends MockeryTestCase
{
    use Diffing;

    /**
     * @test
     * @dataProvider equalConstraints
     * @param string $stringA
     * @param string $stringB
     */
    public function constraints_can_be_equal($stringA, $stringB)
    {
        $a = PackageVersionConstraint::parse($stringA);
        $b = PackageVersionConstraint::parse($stringB);
        $this->assertTrue($a->equals($b), $this->diff($a, $b, 'Package version constraints ought to be equal'));
    }

    public function equalConstraints()
    {
        return [
            '^3.1.1 == ^3.1.1'         => ['^3.1.1', '^3.1.1',],
            '^3.1 == ^3.1.0'           => ['^3.1', '^3.1.0',],
            '1.0 == 1.0'               => ['1.0', '1.0',],
            '1.0 == 1.0.0'             => ['1.0', '1.0.0',],
            'dev-master == dev-master' => ['dev-master', 'dev-master',],
        ];
    }

    /**
     * @test
     * @dataProvider unequalConstraints
     * @param string $stringA
     * @param string $stringB
     */
    public function constraints_can_not_be_equal($stringA, $stringB)
    {
        $a = PackageVersionConstraint::parse($stringA);
        $b = PackageVersionConstraint::parse($stringB);
        $this->assertFalse($a->equals($b), $this->diff($a, $b, 'Package version constraints ought not to be equal'));
    }

    public function unequalConstraints()
    {
        return [
            '^3.1.1 != ~3.1.1'          => ['^3.1.1', '~3.1.1',],
            '1 != 2'                    => ['1', '2',],
            'dev-master != dev-develop' => ['dev-master', 'dev-develop',],
            '* != 1.0'                  => ['*', '1.0',],
        ];
    }
}
