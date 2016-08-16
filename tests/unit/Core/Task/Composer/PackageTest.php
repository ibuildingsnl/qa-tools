<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task\Composer;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Task\Composer\Package;
use Ibuildings\QaTools\UnitTest\Diffing;
use Mockery as m;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 */
class PackageTest extends TestCase
{
    use Diffing;

    /** @test */
    public function has_name()
    {
        $this->assertSame('a/a', (new Package('a/a', '1'))->getName());
    }

    /** @test */
    public function name_must_be_a_valid_composer_package_name()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Package name "$$$" is invalid');

        new Package('$$$', '1');
    }

    /** @test */
    public function normalises_its_version_constraint()
    {
        $this->assertSame('== 1.0.0.0', (new Package('a/a', '1'))->getVersionConstraint());
    }

    /** @test */
    public function version_constraint_must_be_a_valid_composer_package_version_constraint()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('version constraint "---" is invalid');

        new Package('a/a', '---');
    }

    /** @test */
    public function can_compare_version_constraint()
    {
        $this->assertTrue((new Package('a/a', '1'))->versionConstraintEquals('1.0'));
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
            'a/a:^3.1.1 == a/a:^3.1.1' => [new Package('a/a', '^3.1.1'), new Package('a/a', '^3.1.1')],
            'a/a:^3.1 == a/a:^3.1.0' => [new Package('a/a', '^3.1'), new Package('a/a', '^3.1.0')],
            'b/b:1.0 == b/b:1.0' => [new Package('b/b', '1.0'), new Package('b/b', '1.0')],
            'b/b:1.0 == b/b:1.0.0' => [new Package('b/b', '1.0'), new Package('b/b', '1.0.0')],
            'c/c:dev-master == c/c:dev-master' => [new Package('c/c', 'dev-master'), new Package('c/c', 'dev-master')],
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
            'a/a:1 != b/b:1' => [new Package('a/a', '1'), new Package('b/b', '1')],
            'a/a:1 != a/a:2' => [new Package('a/a', '1'), new Package('a/a', '2')],
            'a/a:~1.0 != a/a:~1.0.0' => [new Package('a/a', '~1.0'), new Package('a/a', '~1.0.0')],
            'a/a:~1.0 != a/a:dev-master' => [new Package('a/a', '~1.0'), new Package('a/a', 'dev-master')],
        ];
    }
}
