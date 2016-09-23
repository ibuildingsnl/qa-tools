<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Exception as CoreException;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

final class InstallComposerDevDependencyTaskExecutor implements Executor
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
        return $task instanceof InstallComposerDevDependencyTask;
    }

    public function arePrerequisitesMet(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->say("Verifying installation of Composer development dependencies won't cause a conflict...");

        $packages = $this->getPackagesToAddAsDevDependency($tasks);

        $this->composerProject =
            $this->composerProjectFactory->forDirectory($project->getRootDirectory()->getDirectory());

        try {
            $this->composerProject->verifyDevDependenciesWillNotConflict($packages);
        } catch (CoreException $e) {
            // The Composer project does not communicate precisely what went wrong,
            // so inform the user of the most probable cause (a package conflict) and
            // let the exception bubble up.
            $interviewer->warn(
                'Something went wrong while performing a dry-run install. ' .
                'Most likely, one of the required packages caused a conflict.'
            );

            throw $e;
        }

        return true;
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
            /** @var InstallComposerDevDependencyTask $task */
            $packages = $packages->add(Package::of($task->getPackageName(), $task->getPackageVersionConstraint()));
        }

        return $packages;
    }
}
