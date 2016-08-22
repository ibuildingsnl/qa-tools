<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\CliComposerProject;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\SystemTest\Composer;
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
        $this->workingDirectory = sys_get_temp_dir() . '/qa-tools_' . microtime(true) . '_install-composer-task';
        $this->project = new CliComposerProject($this->workingDirectory, __DIR__ . '/../../../../vendor/bin/composer');
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
    public function can_require_dev_dependencies()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

        $composerJson = file_get_contents('composer.json');
        $this->assertContains('"require-dev"', $composerJson);
        $this->assertContains('"phpmd/phpmd": "^2.0"', $composerJson);
        $this->assertFileExists('vendor/phpmd/phpmd/composer.json');
    }

    /** @test */
    public function can_get_the_current_composer_configuration_of_a_project_without_locked_deps()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        $configuration = $this->project->getConfiguration();
        $this->assertContains('{', $configuration->getComposerJson());
        $this->assertFalse($configuration->hasLockedDependencies());
    }

    /** @test */
    public function can_get_the_current_composer_configuration_of_a_project_with_locked_deps()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

        $configuration = $this->project->getConfiguration();
        $this->assertTrue($configuration->hasLockedDependencies());
    }

    /** @test */
    public function can_restore_a_composer_configuration()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        $configurationBackup = $this->project->getConfiguration();

        $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));
        $this->project->restoreConfiguration($configurationBackup);

        $this->assertFileNotExists('vendor/psr/log/composer.json');
    }

    /** @test */
    public function can_add_conflicts()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        Composer::addConflict('phpmd/phpmd', '^2.0');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your requirements could not be resolved to an installable set of packages.');
        $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));
    }

    /** @test */
    public function can_verify_dev_dep_wont_conflict()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        Composer::addConflict('phpmd/phpmd', '^2.0');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your requirements could not be resolved to an installable set of packages.');
        $this->project->verifyDevDependenciesWillNotConflict(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));
    }

    /** @test */
    public function dev_dep_conflict_verification_doesnt_change_configuration()
    {
        Composer::initialise();
        $this->setupLocalPackages();

        $expectedConfiguration = $this->project->getConfiguration();
        $this->project->verifyDevDependenciesWillNotConflict(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

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

    private function setupLocalPackages()
    {
        // Emulate all the tools' Composer packages locally to guarantee test
        // reliability by removing the Internet factor and to speed up tests.
        $configuration = json_decode(file_get_contents('composer.json'));
        $configuration->repositories = [
            ['packagist' => false],
            ['type' => 'path', 'url' => __DIR__ . '/../../../composer/packages/phpmd'],
        ];
        file_put_contents('composer.json', json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
