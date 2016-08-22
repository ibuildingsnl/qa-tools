<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Composer\Configuration as ComposerConfiguration;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\ComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

final class InstallComposerDevDependencyExecutor implements Executor
{
    /** @var ComposerProjectFactory */
    private $composerProjectFactory;
    /** @var ComposerProject|null */
    private $composerProject;
    /** @var ComposerConfiguration */
    private $configurationBackup;

    public function __construct(ComposerProjectFactory $composerProjectFactory)
    {
        $this->composerProjectFactory = $composerProjectFactory;
    }

    public function supports(Task $task)
    {
        return $task instanceof ComposerDevDependencyTask;
    }

    public function checkPrerequisites(TaskList $tasks, Interviewer $interviewer)
    {
        $interviewer->say("Verifying installation of Composer development dependencies won't cause a conflict...");

        $packages = $this->getPackagesToAddAsDevDependency($tasks);

        $this->composerProject = $this->composerProjectFactory->forDirectory('.');
        $this->composerProject->verifyDevDependenciesWouldntConflict($packages);
    }

    public function execute(TaskList $tasks, Interviewer $interviewer)
    {
        $interviewer->say("Installing Composer development dependencies...");

        $packages = $this->getPackagesToAddAsDevDependency($tasks);

        $this->configurationBackup = $this->composerProject->getConfiguration();
        $this->composerProject->requireDevDependencies($packages);
    }

    public function cleanUp(TaskList $tasks, Interviewer $interviewer)
    {
    }

    public function rollBack(TaskList $tasks, Interviewer $interviewer)
    {
        $interviewer->say("Restoring Composer configuration...");
        $this->composerProject->restoreConfiguration($this->configurationBackup);
    }

    /**
     * @return PackageSet
     */
    private function getPackagesToAddAsDevDependency(TaskList $tasks)
    {
        $packages = new PackageSet();
        foreach ($tasks as $task) {
            /** @var ComposerDevDependencyTask $task */
            $packages = $packages->add($task->getPackage());
        }

        return $packages;
    }
}
