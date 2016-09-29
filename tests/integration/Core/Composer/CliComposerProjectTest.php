<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\CliComposerProject;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\RuntimeException;
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

        $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

        $composerJson = file_get_contents('composer.json');
        $this->assertContains('"require-dev"', $composerJson);
        $this->assertContains('"phpmd/phpmd": "^2.0"', $composerJson);
        $this->assertFileExists('vendor/phpmd/phpmd/composer.json');
    }

    /** @test */
    public function can_restore_a_composer_configuration()
    {
        Composer::initialise();

        $this->project->backUpConfiguration();

        $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));
        $this->assertFileExists('vendor/phpmd/phpmd/composer.json');

        $this->project->restoreConfiguration();

        $this->assertFileNotExists('vendor/phpmd/phpmd/composer.json');
    }

    /** @test */
    public function can_add_conflicts()
    {
        Composer::initialise();
        Composer::addConflict('phpmd/phpmd', '^2.0');

        try {
            $this->project->requireDevDependencies(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

            $this->fail(sprintf('No exception of type "%s" was thrown', RuntimeException::class));
        } catch (RuntimeException $e) {
            $this->assertContains('Failed to require development dependencies', $e->getMessage());
            $this->assertContains('Your requirements could not be resolved to an installable set of packages.', $e->getCause());
        }
    }

    /** @test */
    public function can_verify_dev_dep_wont_conflict()
    {
        Composer::initialise();
        Composer::addConflict('phpmd/phpmd', '^2.0');

        try {
            $this->project->verifyDevDependenciesWillNotConflict(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

            $this->fail(sprintf('No exception of type "%s" was thrown', RuntimeException::class));
        } catch (RuntimeException $e) {
            $this->assertContains('Failed to dry-run Composer packages installation', $e->getMessage());
            $this->assertContains('Your requirements could not be resolved to an installable set of packages.', $e->getCause());
        }
    }

    /** @test */
    public function dev_dep_conflict_verification_doesnt_change_configuration()
    {
        Composer::initialise();

        $this->project->verifyDevDependenciesWillNotConflict(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

        $this->assertNotContains('"phpmd/phpmd"', file_get_contents('composer.json'));
    }
}
