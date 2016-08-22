<?php

namespace Ibuildings\QaTools\Core\Requirement\Executor;

use Ibuildings\QaTools\Core\Composer\Configuration as ComposerConfiguration;
use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Composer\Project as ComposerProject;
use Ibuildings\QaTools\Core\Composer\ProjectFactory as ComposerProjectFactory;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Requirement\ComposerDevDependencyRequirement;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\RequirementList;

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

    public function supports(Requirement $requirement)
    {
        return $requirement instanceof ComposerDevDependencyRequirement;
    }

    public function checkPrerequisites(RequirementList $requirements, Interviewer $interviewer)
    {
        $interviewer->say("Verifying installation of Composer development dependencies won't cause a conflict...");

        $packages = $this->getPackagesToAddAsDevDependency($requirements);

        $this->composerProject = $this->composerProjectFactory->forDirectory('.');
        $this->composerProject->verifyDevDependenciesWouldntConflict($packages);
    }

    public function execute(RequirementList $requirements, Interviewer $interviewer)
    {
        $interviewer->say("Installing Composer development dependencies...");

        $packages = $this->getPackagesToAddAsDevDependency($requirements);

        $this->configurationBackup = $this->composerProject->getConfiguration();
        $this->composerProject->requireDevDependencies($packages);
    }

    public function cleanUp(RequirementList $requirements, Interviewer $interviewer)
    {
    }

    public function rollBack(RequirementList $requirements, Interviewer $interviewer)
    {
        $interviewer->say("Restoring Composer configuration...");
        $this->composerProject->restoreConfiguration($this->configurationBackup);
    }

    /**
     * @return PackageSet
     */
    private function getPackagesToAddAsDevDependency(RequirementList $requirements)
    {
        $packages = new PackageSet();
        foreach ($requirements as $requirement) {
            /** @var ComposerDevDependencyRequirement $requirement */
            $packages = $packages->add($requirement->getPackage());
        }

        return $packages;
    }
}
