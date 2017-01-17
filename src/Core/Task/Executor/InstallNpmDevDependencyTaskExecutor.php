<?php

namespace Ibuildings\QaTools\Core\Task\Executor;

use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Npm\CliNpmProject;
use Ibuildings\QaTools\Core\Npm\CliNpmProjectFactory;
use Ibuildings\QaTools\Core\Npm\RuntimeException;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Task\InstallNpmDevDependencyTask;
use Ibuildings\QaTools\Core\Task\Task;
use Ibuildings\QaTools\Core\Task\TaskList;

final class InstallNpmDevDependencyTaskExecutor implements Executor
{
    /** @var CliNpmProjectFactory $npmProjectFactory */
    private $npmProjectFactory;
    /** @var CliNpmProject $npmProject */
    private $npmProject;

    public function __construct(CliNpmProjectFactory $npmProjectFactory)
    {
        $this->npmProjectFactory = $npmProjectFactory;
    }

    public function supports(Task $task)
    {
        return $task instanceof InstallNpmDevDependencyTask;
    }

    public function arePrerequisitesMet(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $interviewer->notice(
            " * Verifying installation of NPM development dependencies won't cause a conflict..."
        );

        $this->npmProject = $this->npmProjectFactory->forDirectory($project->getRootDirectory()->getDirectory());

        if (!$this->npmProject->isInitialised()) {
            /** @var YesOrNoAnswer $answer */
            $answer = $interviewer->ask(
                QuestionFactory::createYesOrNo(
                    'There is no NPM project initialised in this directory. Initialise one?',
                    YesOrNoAnswer::YES
                )
            );

            if ($answer->is(YesOrNoAnswer::NO)) {
                $interviewer->warn('Cannot continue without an initialised NPM project. Aborting...');

                return false;
            }

            try {
                $this->npmProject->initialise();
            } catch (RuntimeException $e) {
                $interviewer->warn('Something went wrong while initialising the NPM project:');
                $interviewer->warn($e->getCause());

                return false;
            }

            $interviewer->notice(
                "Verifying installation of NPM development dependencies won't cause a conflict..."
            );
        }

        $packages = $this->getPackagesFromTasks($tasks);

        return $this->npmProject->verifyDevDependenciesCanBeInstalled($packages);
    }

    public function execute(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
        $this->npmProject->installDevDependencies($this->getPackagesFromTasks($tasks));
    }

    public function cleanUp(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }

    public function rollBack(TaskList $tasks, Project $project, Interviewer $interviewer)
    {
    }

    private function getPackagesFromTasks($tasks)
    {
        $packages = [];
        /** @var InstallNpmDevDependencyTask $task */
        foreach ($tasks as $task) {
            $packages[] = $task->getPackageName().'@'.$task->getPackageVersionConstraint();
        }
        return $packages;
    }
}
