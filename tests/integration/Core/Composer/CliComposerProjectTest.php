<?php

namespace Ibuildings\QaTools\IntegrationTest\Core\Composer;

use Ibuildings\QaTools\Core\Composer\CliComposerProject;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\RuntimeException;
use Ibuildings\QaTools\SystemTest\Composer;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Ibuildings\QaTools\UnitTest\Diffing;
use Mockery;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group Composer
 */
class CliComposerProjectTest extends MockeryTestCase
{
    use Diffing;

    /** @var string */
    private $workingDirectory;

    /** @var CliComposerProject */
    private $project;
 
    /** @var LoggerInterface|MockInterface */
    private $logger;

    protected function runTest()
    {
        $oldWd = getcwd();

        $this->logger = Mockery::spy(LoggerInterface::class);
        $this->workingDirectory = sys_get_temp_dir() . '/qa-tools_' . microtime(true) . '_install-composer-task';
        $this->project = new CliComposerProject(
            $this->workingDirectory,
            __DIR__ . '/../../../../vendor/bin/composer',
            $this->logger
        );

        mkdir($this->workingDirectory);
        chdir($this->workingDirectory);

        try {
            parent::runTest();

            // Remove the directory when the test passed.
            $filesystem = new Filesystem();
            $filesystem->chmod($this->workingDirectory, 0775);
            $filesystem->remove($this->workingDirectory);
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
        $this->logger->shouldNotHaveReceived('error');
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
            $this->logger->shouldHaveReceived('error');
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
            $this->assertContains('Failed to require development dependencies', $e->getMessage());
            $this->assertContains('Your requirements could not be resolved to an installable set of packages.', $e->getCause());
        }

        Composer::assertPackageIsNotRequiredAsDevDependency('phpmd/phpmd');
    }

    /** @test */
    public function dev_dep_conflict_verification_doesnt_change_configuration()
    {
        Composer::initialise();

        $this->project->verifyDevDependenciesWillNotConflict(new PackageSet([Package::of('phpmd/phpmd', '^2.0')]));

        $this->assertNotContains('"phpmd/phpmd"', file_get_contents('composer.json'));
    }

    /** @test */
    public function can_tell_whether_a_project_is_not_initialised()
    {
        $this->assertFalse(
            $this->project->isInitialised(),
            'An empty directory should not be an initialised Composer project'
        );
    }

    /** @test */
    public function can_tell_a_project_is_initialize()
    {
        Composer::initialise();

        $this->assertTrue(
            $this->project->isInitialised(),
            'A project that was just initialised should be an initialised Composer project'
        );
    }
}
