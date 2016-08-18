<?php

namespace Ibuildings\QaTools\UnitTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\UnitTest\Diffing;
use PHPUnit\Framework\TestCase as TestCase;

class PackageSetTest extends TestCase
{
    use Diffing;

    /**
     * @test
     * @group value
     * @dataProvider unequalSets
     *
     * @param array $set0
     * @param array $set1
     */
    public function it_can_test_for_inequality(array $set0, array $set1)
    {
        $this->assertFalse(
            (new PackageSet($set0))->equals(new PackageSet($set1)),
            "Entity sets are not equal, but are reported to be"
        );
    }

    public function unequalSets()
    {
        return [
            [
                [Package::of('phpunit/phpunit', '5.3.*'), Package::of('phpmd/phpmd', '^2.0')],
                [Package::of('phpmd/phpmd', '^2.0')],
            ],
            [
                [Package::of('phpmd/phpmd', '^2.0'), Package::of('phpmd/phpmd', '^2.0')],
                [Package::of('phpunit/phpunit', '5.3.*')],
            ],
            [
                [Package::of('phpmd/phpmd', '^2.0')],
                [Package::of('symfony/console', '*'), Package::of('symfony/console', '*')],
            ],
            [
                [Package::of('phpmd/phpmd', '^2.0')],
                [],
            ],
            [
                [],
                [Package::of('phpmd/phpmd', '^2.0')],
            ],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider equalSets
     *
     * @param array $set0
     * @param array $set1
     */
    public function it_can_test_for_equality(array $set0, array $set1)
    {
        $this->assertTrue(
            (new PackageSet($set0))->equals(new PackageSet($set1)),
            "Entity sets should be equal, but they're not"
        );
    }

    public function equalSets()
    {
        return [
            [
                [Package::of('phpmd/phpmd', '^2.0'), Package::of('phpmd/phpmd', '^2.0')],
                [Package::of('phpmd/phpmd', '^2.0')],
            ],
            [
                [Package::of('phpmd/phpmd', '^2.0')],
                [Package::of('phpmd/phpmd', '^2.0'), Package::of('phpmd/phpmd', '^2.0')],
            ],
            [
                [],
                [],
            ],
            [
                [Package::of('phpmd/phpmd', '^2.0'), Package::of('phpunit/phpunit', '5.3.*')],
                [Package::of('phpmd/phpmd', '^2.0'), Package::of('phpunit/phpunit', '5.3.*')],
            ],
            [
                [Package::of('phpmd/phpmd', '^2.0'), Package::of('phpunit/phpunit', '5.3.*'), Package::of('phpunit/phpunit', '5.3.*')],
                [Package::of('phpunit/phpunit', '5.3.*'),  Package::of('phpmd/phpmd', '^2.0')],
            ],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider setsThatContainAnEntity
     */
    public function sets_can_contain_entities(PackageSet $set, Package $package)
    {
        $this->assertTrue(
            $set->contains($package),
            sprintf('Set of %d should contain entity "%s", but PackageSet reports otherwise', count($set), $package)
        );
    }

    public function setsThatContainAnEntity()
    {
        return [
            '1-set' => [
                new PackageSet([Package::of('phpmd/phpmd', '^2.0')]),
                Package::of('phpmd/phpmd', '^2.0'),
            ],
            '2-set, first' => [
                new PackageSet([Package::of('phpmd/phpmd', '^2.0'), Package::of('phpunit/phpunit', '5.3.*')]),
                Package::of('phpmd/phpmd', '^2.0'),
            ],
            '2-set, second' => [
                new PackageSet([Package::of('phpmd/phpmd', '^2.0'), Package::of('phpunit/phpunit', '5.3.*')]),
                Package::of('phpunit/phpunit', '5.3.*'),
            ],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider setsThatDontContainAnEntity
     *
     * @param PackageSet $set
     * @param Package    $package
     */
    public function sets_can_not_contain_entities(PackageSet $set, Package $package)
    {
        $this->assertFalse(
            $set->contains($package),
            sprintf('Set of %d should not contain entity "%s", but PackageSet reports otherwise', count($set), $package)
        );
    }

    public function setsThatDontContainAnEntity()
    {
        return [
            '2-set' => [
                new PackageSet([Package::of('phpmd/phpmd', '^2.0'), Package::of('phpunit/phpunit', '5.3.*')]),
                Package::of('symfony/console', '*'),
            ],
            '0-set' => [
                new PackageSet([]),
                Package::of('phpmd/phpmd', '^2.0'),
            ],
            '1-set' => [
                new PackageSet([Package::of('phpmd/phpmd', '^2.0')]),
                Package::of('phpunit/phpunit', '5.3.*'),
            ],
        ];
    }

    /** @test */
    public function set_can_be_filtered()
    {
        $packageA = Package::of('a/a', '1');
        $packageB = Package::of('b/b', '2');

        $expected = new PackageSet([$packageA]);
        $actual = (new PackageSet([$packageA, $packageB]))->filter(function (Package $package) {
            return $package->getName()->equals(new PackageName('a/a'));
        });

        $this->assertTrue($expected->equals($actual), $this->diff($expected, $actual, 'Filtered set of packages is not as expected'));
    }

    /** @test */
    public function has_package_descriptors()
    {
        $packages = new PackageSet(
            [
                Package::of('ibuildings/qa-tools', '1.1.27'),
                Package::of('symfony/console', '4|5'),
            ]
        );

        $this->assertSame(['ibuildings/qa-tools:1.1.27', 'symfony/console:4|5'], $packages->getDescriptors());
    }
}
