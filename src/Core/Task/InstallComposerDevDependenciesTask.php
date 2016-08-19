<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Composer\Configuration;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project;
use Ibuildings\QaTools\Core\Exception\RuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

final class InstallComposerDevDependenciesTask implements Task
{
    /**
     * @var PackageSet
     */
    private $packages;

    /**
     * @var Project
     */
    private $project;

    /**
     * @var Configuration|null
     */
    private $configurationBackup;

    /**
     * @var boolean
     */
    private $executed;

    public function __construct(PackageSet $packages, Project $project)
    {
        Assertion::greaterThan($packages->count(), 0, 'Cannot install an empty set of packages');

        $this->packages = $packages;
        $this->project = $project;
    }

    public function getDescription()
    {
        return sprintf('Install %d Composer package(s)', $this->packages->count());
    }

    public function checkPrerequisites(Interviewer $interviewer)
    {
        $interviewer->say('Verifying that installing the following Composer packages would not cause conflicts:');
        foreach ($this->packages as $package) {
            $interviewer->say(sprintf(' - %s', $package->getDescriptor()));
        }

        $this->project->verifyDevDependenciesWouldntConflict($this->packages);
    }

    public function execute(Interviewer $interviewer)
    {
        if ($this->executed) {
            throw new RuntimeException('This task has already been executed');
        }

        $this->executed = true;

        $interviewer->say('Installing Composer packages...');
        $this->configurationBackup = $this->project->getConfiguration();
        $this->project->requireDevDependencies($this->packages);
    }

    public function rollBack(Interviewer $interviewer)
    {
        if (!$this->executed) {
            throw new RuntimeException('This task has not yet been executed');
        }
        if (!$this->configurationBackup) {
            return;
        }

        $interviewer->say('Restoring Composer configuration...');
        $this->project->restoreConfiguration($this->configurationBackup);
    }

    /**
     * @return PackageSet
     */
    public function getPackages()
    {
        return $this->packages;
    }

    public function equals(Task $task)
    {
        /** @var self $task */
        return get_class($this) === get_class($task) && $this->packages->equals($task->packages);
    }
}
