<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Assert\Assertion;

class Project
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Directory
     */
    private $rootDirectory;

    /**
     * @var Directory
     */
    private $configurationFilesLocation;

    /**
     * @var ProjectTypeSet
     */
    private $projectTypes;

    /**
     * @var boolean
     */
    private $travisEnabled;

    public function __construct(
        $name,
        Directory $rootDirectory,
        Directory $configurationFilesLocation,
        ProjectTypeSet $projectTypes,
        $travisEnabled
    ) {
        Assertion::string($name);
        Assertion::boolean($travisEnabled);

        $this->name                       = $name;
        $this->rootDirectory              = $rootDirectory;
        $this->configurationFilesLocation = $configurationFilesLocation;
        $this->projectTypes               = $projectTypes;
        $this->travisEnabled              = $travisEnabled;
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function equals(Project $project)
    {
        return $this->name === $project->name
            && $this->rootDirectory->equals($project->rootDirectory)
            && $this->configurationFilesLocation->equals($project->configurationFilesLocation)
            && $this->projectTypes->equals($project->projectTypes)
            && $this->travisEnabled === $project->travisEnabled;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Directory
     */
    public function getRootDirectory()
    {
        return $this->rootDirectory;
    }

    /**
     * @return Directory
     */
    public function getConfigurationFilesLocation()
    {
        return $this->configurationFilesLocation;
    }

    /**
     * @return ProjectTypeSet
     */
    public function getProjectTypes()
    {
        return $this->projectTypes;
    }

    /**
     * @return boolean
     */
    public function isTravisEnabled()
    {
        return $this->travisEnabled;
    }

    public function __toString()
    {
        return sprintf(
            'Project(name="%s", rootDirectory="%s", configurationFilesDirectory="%s", projectTypes=%s, ' .
            'travisEnabled=%d)',
            $this->name,
            $this->rootDirectory->getDirectory(),
            $this->configurationFilesLocation->getDirectory(),
            $this->projectTypes,
            $this->travisEnabled
        );
    }
}
