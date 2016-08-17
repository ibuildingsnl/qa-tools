<?php

namespace Ibuildings\QaTools\UnitTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageVersionConstraint;
use Ibuildings\QaTools\UnitTest\Diffing;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Requirement
 */
class PackageTest extends TestCase
{
    use Diffing;

    /** @test */
    public function can_compare_version_constraint()
    {
        $this->assertTrue((Package::of('a/a', '1'))->versionConstraintEquals(PackageVersionConstraint::parse('1.0')));
    }

    /**
     * @test
     * @dataProvider equalPackages
     * @param Package $a
     * @param Package $b
     */
    public function packages_can_be_equal(Package $a, Package $b)
    {
        $this->assertTrue($a->equals($b), 'Packages ought to equal each other');
    }

    public function equalPackages()
    {
        return [
            'a/a:^3.1.1 == a/a:^3.1.1' => [Package::of('a/a', '^3.1.1'), Package::of('a/a', '^3.1.1')],
            'a/a:^3.1 == a/a:^3.1.0' => [Package::of('a/a', '^3.1'), Package::of('a/a', '^3.1.0')],
            'b/b:1.0 == b/b:1.0' => [Package::of('b/b', '1.0'), Package::of('b/b', '1.0')],
            'b/b:1.0 == b/b:1.0.0' => [Package::of('b/b', '1.0'), Package::of('b/b', '1.0.0')],
            'c/c:dev-master == c/c:dev-master' => [Package::of('c/c', 'dev-master'), Package::of('c/c', 'dev-master')],
        ];
    }

    /**
     * @test
     * @dataProvider unequalPackages
     * @param Package $a
     * @param Package $b
     */
    public function packages_can_not_be_equal(Package $a, Package $b)
    {
        $this->assertFalse($a->equals($b), 'Packages ought not to equal each other');
    }

    public function unequalPackages()
    {
        return [
            'a/a:1 != b/b:1' => [Package::of('a/a', '1'), Package::of('b/b', '1')],
            'a/a:1 != a/a:2' => [Package::of('a/a', '1'), Package::of('a/a', '2')],
            'a/a:~1.0 != a/a:~1.0.0' => [Package::of('a/a', '~1.0'), Package::of('a/a', '~1.0.0')],
            'a/a:~1.0 != a/a:dev-master' => [Package::of('a/a', '~1.0'), Package::of('a/a', 'dev-master)')],
        ];
    }
}
