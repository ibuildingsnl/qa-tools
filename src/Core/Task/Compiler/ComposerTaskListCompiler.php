<?php

namespace Ibuildings\QaTools\Core\Task\Compiler;

use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\ProjectFactory;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Requirement\ComposerDevDependenciesRequirement;
use Ibuildings\QaTools\Core\Requirement\RequirementList;
use Ibuildings\QaTools\Core\Requirement\Specification\TypeSpecification;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependenciesTask;
use Ibuildings\QaTools\Core\Task\TaskList;

final class ComposerTaskListCompiler implements TaskListCompiler
{
    /**
     * @var ProjectFactory
     */
    private $composerProjectFactory;

    public function __construct(ProjectFactory $composerProjectFactory)
    {
        $this->composerProjectFactory = $composerProjectFactory;
    }

    public function compile(RequirementDirectory $requirementDirectory, ScopedInterviewer $interviewer)
    {
        /** @var RequirementList|ComposerDevDependenciesRequirement[] $packagesRequirements */
        $packagesRequirements = $requirementDirectory->matchRequirements(
            new TypeSpecification(ComposerDevDependenciesRequirement::class)
        );

        $packages = new PackageSet();
        foreach ($packagesRequirements as $packagesRequirement) {
            $packages = $packages->merge($packagesRequirement->getPackages());
        }

        if ($packages->count() === 0) {
            return new TaskList();
        }

        $projectRootDirectory = $requirementDirectory->getProject()->getRootDirectory();
        $composerProject = $this->composerProjectFactory->forDirectory($projectRootDirectory->getDirectory());

        $task = new InstallComposerDevDependenciesTask($packages, $composerProject);
        $tasks = new TaskList([$task]);

        return $tasks;
    }
}
