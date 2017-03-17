<?php

namespace Ibuildings\QaTools\UnitTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\Configuration;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\RequireCache;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class RequireCacheTest extends TestCase
{
    /** @test */
    public function stores_successful_requires()
    {
        $targetConfiguration = Configuration::withoutLockedDependencies('"target"');
        $requiredPackages = new PackageSet([Package::of('phpmd/phpmd', '^2')]);
        $newConfiguration = Configuration::withoutLockedDependencies('"new"');

        $cache = new RequireCache();

        $cache->storeConfiguration($targetConfiguration, $requiredPackages, $newConfiguration);
    }

    /** @test */
    public function stores_successful_requires_of_0_packages()
    {
        $targetConfiguration = Configuration::withoutLockedDependencies('"target"');
        $requiredPackages = new PackageSet();
        $newConfiguration = Configuration::withoutLockedDependencies('"new"');

        $cache = new RequireCache();

        $cache->storeConfiguration($targetConfiguration, $requiredPackages, $newConfiguration);
    }

    /** @test */
    public function contains_successful_requires()
    {
        $targetConfiguration = Configuration::withoutLockedDependencies('"target"');
        $requiredPackages = new PackageSet([Package::of('phpmd/phpmd', '^2'), Package::of('phpunit/phpunit', '5')]);
        $newConfiguration = Configuration::withoutLockedDependencies('"new"');

        $cache = new RequireCache();
        $cache->storeConfiguration($targetConfiguration, $requiredPackages, $newConfiguration);

        $this->assertTrue($cache->containsConfiguration($targetConfiguration, $requiredPackages));
    }

    /** @test */
    public function can_return_successful_requires()
    {
        $targetConfiguration = Configuration::withoutLockedDependencies('"target"');
        $requiredPackages = new PackageSet([Package::of('phpunit/phpunit', '5')]);
        $newConfiguration = Configuration::withoutLockedDependencies('"new"');

        $cache = new RequireCache();
        $cache->storeConfiguration($targetConfiguration, $requiredPackages, $newConfiguration);

        $returnedConfiguration = $cache->getConfiguration($targetConfiguration, $requiredPackages);

        $this->assertTrue($returnedConfiguration->equals($newConfiguration));
    }

    /** @test */
    public function throws_an_exception_when_getting_a_nonexistent_configuration()
    {
        $targetConfiguration = Configuration::withoutLockedDependencies('"target"');
        $requiredPackages = new PackageSet(
            [Package::of('phpmd/phpmd', '^2'), Package::of('ibuildingsnl/qa-tools', '3.0.0-beta1')]
        );

        $cache = new RequireCache();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No configuration is cached for the given requirement wishes ' .
            '"phpmd/phpmd:^2", "ibuildingsnl/qa-tools:3.0.0-beta1"'
        );

        $cache->getConfiguration($targetConfiguration, $requiredPackages);
    }
}
