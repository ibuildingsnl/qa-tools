<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Composer\PackageSet;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;

final class InstallComposerPackagesTask implements Task
{
    /**
     * @var PackageSet
     */
    private $packages;

    public function __construct(PackageSet $packages)
    {
        $this->packages = $packages;
    }

    public function getDescription()
    {
        return sprintf('Install %d Composer packages', $this->packages->count());
    }

    public function checkPrerequisites(Interviewer $interviewer)
    {
    }

    public function execute(Interviewer $interviewer)
    {
        return new NoOpTask();
    }

    /**
     * @return PackageSet
     */
    public function getPackages()
    {
        return $this->packages;
    }
}
