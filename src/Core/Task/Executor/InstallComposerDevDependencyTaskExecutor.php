<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Composer\Configuration;
use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Composer\RuntimeException as ComposerRuntimeException;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) -- Due to wide-spread value object usage a higher coupling is
 *     acceptable
 */
final class InstallComposerDevDependencyTaskExecutor implements Executor
{
    /** @var ComposerProjectFactory */
    private $composerProjectFactory;
    /** @var ComposerProject|null */
    private $composerProject;
    /** @var Configuration|null */
    private $configurationBackup;

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
        $interviewer->notice(
            " * Verifying installation of Composer development dependencies won't cause a conflict..."
        );

        $packages = $this->getPackagesToAddAsDevDependency($tasks);
        foreach ($packages as $package) {
            /** @var Package $package */
            $interviewer->giveDetails(sprintf('     - %s', $package->getDescriptor()));
        }

        $this->composerProject =
            $this->composerProjectFactory->forDirectory($project->getRootDirectory()->getDirectory());

        if (!$this->composerProject->isInitialised()) {
            $questionText = 'There is no Composer project initialised in this directory. Initialise one?';
            /** @var YesOrNoAnswer $answer */
            $answer = $interviewer->ask(QuestionFactory::createYesOrNo($questionText, YesOrNoAnswer::YES));

            if ($answer->is(YesOrNoAnswer::NO)) {
                $interviewer->warn('Cannot continue without an initialised Composer project. Aborting...');

                return false;
            }

            try {
                $this->composerProject->initialise();
            } catch (ComposerRuntimeException $e) {
                $interviewer->warn('Something went wrong while initialising the Composer project');

                return false;
            }

            $interviewer->notice(
                "Verifying installation of Composer development dependencies won't cause a conflict..."
            );
        }

        try {
            $this->composerProject->verifyDevDependenciesWillNotConflict($packages);
        } catch (ComposerRuntimeException $e) {
            $interviewer->warn('Something went wrong while performing a dry-run install:');
            $interviewer->giveDetails('');

            $indentedCause = preg_replace('/^/m', '  ', $e->getCause());
            $interviewer->giveDetails($indentedCause);

            $interviewer->giveDetails('');

            return false;
        }

        return true;
    }

    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->notice(" * Installing Composer development dependencies...");

        $packages = $this->getPackagesToAddAsDevDependency($tasks);

        $this->configurationBackup = $this->composerProject->readConfiguration();
        $this->composerProject->requireDevDependencies($packages);
    }

    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }

    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->notice(" * Restoring Composer configuration...");

        $this->composerProject->restoreConfiguration($this->configurationBackup);
    }

    /**
     * @param TaskList $tasks
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
