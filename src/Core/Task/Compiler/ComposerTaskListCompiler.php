<?php

namespace Ibuildings\QaTools\Core\Task\Compiler;

use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Interviewer\ScopedInterviewer;
use Ibuildings\QaTools\Core\Requirement\ComposerPackagesRequirement;
use Ibuildings\QaTools\Core\Requirement\RequirementList;
use Ibuildings\QaTools\Core\Requirement\Specification\TypeSpecification;
use Ibuildings\QaTools\Core\Task\InstallComposerPackagesTask;
use Ibuildings\QaTools\Core\Task\TaskList;

final class ComposerTaskListCompiler implements TaskListCompiler
{
    public function compile(RequirementDirectory $requirementDirectory, ScopedInterviewer $interviewer)
    {
        /** @var RequirementList|ComposerPackagesRequirement[] $packagesRequirements */
        $packagesRequirements = $requirementDirectory->matchRequirements(
            new TypeSpecification(ComposerPackagesRequirement::class)
        );

        $packages = new PackageSet();
        foreach ($packagesRequirements as $packagesRequirement) {
            $packages = $packages->merge($packagesRequirement->getPackages());
        }

        if ($packages->count() === 0) {
            return new TaskList();
        }

        $task = new InstallComposerPackagesTask($packages);
        $tasks = new TaskList([$task]);

        return $tasks;
    }
}
