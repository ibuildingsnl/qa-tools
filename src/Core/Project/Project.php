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
     * @var string
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

    public function __construct($name, $configurationFilesLocation, ProjectTypeSet $projectTypes, $travisEnabled)
    {
        Assertion::string($name);
        Assertion::string($configurationFilesLocation);
        Assertion::boolean($travisEnabled);

        $this->name                       = $name;
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
            && $this->configurationFilesLocation === $project->configurationFilesLocation
            && $this->projectTypes->equals($project->projectTypes);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
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
}
