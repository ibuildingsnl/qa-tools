<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\CliComposerProject;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\RuntimeAssertion;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\UnitTest\Diffing;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Composer
 */
class CliComposerProjectTest extends TestCase
{
    use Diffing;

    /** @var string */
    private $workingDirectory;
    /** @var CliComposerProject */
    private $project;

    protected function setUp()
    {
        $this->workingDirectory = sys_get_temp_dir() . '/qa-tools_' . microtime(true) . '_install_composer_task';
        $this->project = new CliComposerProject($this->workingDirectory);
    }

    protected function runTest()
    {
        $oldWd = getcwd();

        try {
            mkdir($this->workingDirectory);
            chdir($this->workingDirectory);
            parent::runTest();
        } finally {
            chdir($oldWd);
        }
    }

    /** @test */
    public function can_initialise_a_composer_project()
    {
        RuntimeAssertion::pathNotExists('composer.json');

        $this->project->initialise(new PackageName('ploctones/050'));

        RuntimeAssertion::file('composer.json');
        $this->assertContains('ploctones/050', file_get_contents('composer.json'));
    }

    /** @test */
    public function can_require_dev_dependencies()
    {
        $this->project->initialise(new PackageName('tomwaits/martha'));
        $this->project->requireDevDependencies(new PackageSet([Package::of('psr/log', '*')]));

        $composerJson = file_get_contents('composer.json');
        $this->assertContains('"require": {}', $composerJson);
        $this->assertContains('"require-dev"', $composerJson);
        $this->assertContains('"psr/log": "*"', $composerJson);
        RuntimeAssertion::file('vendor/psr/log/composer.json');
    }

    /** @test */
    public function can_get_the_current_composer_configuration_of_a_project_without_locked_deps()
    {
        $this->project->initialise(new PackageName('tomwaits/hold-on'));

        $configuration = $this->project->getConfiguration();
        $this->assertContains('"name": "tomwaits/hold-on"', $configuration->getComposerJson());
        $this->assertFalse($configuration->hasLockedDependencies());
    }

    /** @test */
    public function can_get_the_current_composer_configuration_of_a_project_with_locked_deps()
    {
        $this->project->initialise(new PackageName('tomwaits/hold-on'));
        $this->project->requireDevDependencies(new PackageSet([Package::of('psr/log', '*')]));

        $configuration = $this->project->getConfiguration();
        $this->assertTrue($configuration->hasLockedDependencies());
    }

    /** @test */
    public function can_restore_a_composer_configuration()
    {
        $this->project->initialise(new PackageName('tomwaits/hold-on'));
        $configurationBackup = $this->project->getConfiguration();

        $this->project->requireDevDependencies(new PackageSet([Package::of('psr/log', '*')]));
        $this->project->restoreConfiguration($configurationBackup);

        $this->assertFileNotExists('vendor/psr/log/composer.json');
    }

    /** @test */
    public function can_add_conflicts()
    {
        $this->project->initialise(new PackageName('tomwaits/martha'));
        $this->project->addConflict(Package::of('psr/log', '*'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your requirements could not be resolved to an installable set of packages.');
        $this->project->requireDevDependencies(new PackageSet([Package::of('psr/log', '*')]));
    }

    /** @test */
    public function can_verify_dev_dep_wouldnt_conflict()
    {
        $this->project->initialise(new PackageName('tomwaits/martha'));
        $this->project->addConflict(Package::of('psr/log', '*'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your requirements could not be resolved to an installable set of packages.');
        $this->project->verifyDevDependenciesWouldntConflict(new PackageSet([Package::of('psr/log', '*')]));
    }

    /** @test */
    public function dev_dep_conflict_verification_doesnt_change_configuration()
    {
        $this->project->initialise(new PackageName('tomwaits/martha'));
        $this->project->requireDevDependencies(new PackageSet([Package::of('psr/cache', '*')]));
        $expectedConfiguration = $this->project->getConfiguration();
        $this->project->verifyDevDependenciesWouldntConflict(new PackageSet([Package::of('psr/log', '*')]));

        $actualConfiguration = $this->project->getConfiguration();
        $this->assertTrue(
            $expectedConfiguration->equals($actualConfiguration),
            $this->diff(
                $expectedConfiguration,
                $actualConfiguration,
                "Composer configuration was changed while verifying an added development dependency wouldn't conflict"
            )
        );
    }
}
