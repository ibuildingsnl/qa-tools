<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class Project
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $configFileLocation;

    /**
     * @var ProjectType[]
     */
    private $projectTypes;

    /**
     * @var boolean
     */
    private $travisEnabled;

    public function __construct($name, $configFileLocation, array $projectTypes, $travisEnabled)
    {
        Assertion::string($name);
        Assertion::string($configFileLocation);
        Assertion::allIsInstanceOf($projectTypes, ProjectType::class);
        Assertion::boolean($travisEnabled);

        $this->name               = $name;
        $this->configFileLocation = $configFileLocation;
        $this->projectTypes       = $projectTypes;
        $this->travisEnabled      = $travisEnabled;
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
    public function getConfigFileLocation()
    {
        return $this->configFileLocation;
    }

    /**
     * @return string
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
