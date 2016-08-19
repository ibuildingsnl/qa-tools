<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Exception;
use Ibuildings\QaTools\Core\Composer\Configuration;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependenciesTask;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 * @group Composer
 */
class InstallComposerDevDependenciesTaskTest extends TestCase
{
    /** @var Project|MockInterface */
    private $project;
    /** @var Interviewer|MockInterface */
    private $interviewer;

    protected function setUp()
    {
        $this->project = Mockery::spy(Project::class);
        $this->interviewer = Mockery::mock(Interviewer::class)->shouldIgnoreMissing();
    }

    /** @test */
    public function non_conflicting_dependencies_is_a_prerequisite()
    {
        $packages = new PackageSet([Package::of('benwebster/makin-whoopee', '1957')]);
        $task = new InstallComposerDevDependenciesTask($packages, $this->project);

        $task->checkPrerequisites($this->interviewer);

        $this->project->shouldHaveReceived('verifyDevDependenciesWouldntConflict')->with($packages);
    }

    /** @test */
    public function installs_the_new_dependencies()
    {
        $packages = new PackageSet([Package::of('benwebster/makin-whoopee', '1957')]);
        $task = new InstallComposerDevDependenciesTask($packages, $this->project);

        $configurationBackup = Configuration::withoutLockedDependencies('{}');
        $this->project->shouldReceive('getConfiguration')->andReturn($configurationBackup);

        $task->execute($this->interviewer);

        $this->project->shouldHaveReceived('requireDevDependencies')->with($packages);
    }

    /** @test */
    public function can_restores_the_old_configuration_when_installing_the_new_dependencies_fails()
    {
        $packages = new PackageSet([Package::of('benwebster/makin-whoopee', '1957')]);
        $task = new InstallComposerDevDependenciesTask($packages, $this->project);

        $configurationBackup = Configuration::withoutLockedDependencies('{}');
        $this->project->shouldReceive('getConfiguration')->andReturn($configurationBackup);
        $this->project->shouldReceive('requireDevDependencies')->andThrow(new RuntimeException('Oh noes!'));

        try {
            $task->execute($this->interviewer);
        } catch (Exception $e) {
            $task->rollBack($this->interviewer);
        }

        $this->project->shouldHaveReceived('restoreConfiguration')->with($configurationBackup);
    }
}
