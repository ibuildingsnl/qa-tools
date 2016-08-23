<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\ComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

final class InstallComposerDevDependencyExecutor implements Executor
{
    /** @var ComposerProjectFactory */
    private $composerProjectFactory;
    /** @var ComposerProject|null */
    private $composerProject;

    public function __construct(ComposerProjectFactory $composerProjectFactory)
    {
        $this->composerProjectFactory = $composerProjectFactory;
    }

    public function supports(Task $task)
    {
        return $task instanceof ComposerDevDependencyTask;
    }

    public function checkPrerequisites(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->say("Verifying installation of Composer development dependencies won't cause a conflict...");

        $packages = $this->getPackagesToAddAsDevDependency($tasks);

        $this->composerProject =
            $this->composerProjectFactory->forDirectory($project->getRootDirectory()->getDirectory());
        $this->composerProject->verifyDevDependenciesWillNotConflict($packages);
    }

    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->say("Installing Composer development dependencies...");

        $packages = $this->getPackagesToAddAsDevDependency($tasks);

        $this->composerProject->backUpConfiguration();
        $this->composerProject->requireDevDependencies($packages);
    }

    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }

    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->say("Restoring Composer configuration...");
        $this->composerProject->restoreConfiguration();
    }

    /**
     * @return PackageSet
     */
    private function getPackagesToAddAsDevDependency(TaskList $tasks)
    {
        $packages = new PackageSet();
        foreach ($tasks as $task) {
            /** @var ComposerDevDependencyTask $task */
            $packages = $packages->add(Package::of($task->getPackageName(), $task->getPackageVersionConstraint()));
        }

        return $packages;
    }
}
