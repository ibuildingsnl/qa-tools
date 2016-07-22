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
     * @var ProjectType[]
     */
    private $projectTypes;

    /**
     * @var boolean
     */
    private $travisEnabled;

    public function __construct($name, $configurationFilesLocation, array $projectTypes, $travisEnabled)
    {
        Assertion::string($name);
        Assertion::string($configurationFilesLocation);
        Assertion::allIsInstanceOf($projectTypes, ProjectType::class);
        Assertion::boolean($travisEnabled);

        $this->name                       = $name;
        $this->configurationFilesLocation = $configurationFilesLocation;
        $this->projectTypes               = $projectTypes;
        $this->travisEnabled              = $travisEnabled;
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
     * @return ProjectType[]
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
