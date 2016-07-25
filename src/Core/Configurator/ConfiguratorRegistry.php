<?php

namespace Ibuildings\QaTools\Core\Configurator;

use ArrayIterator;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use IteratorAggregate;

final class ConfiguratorRegistry implements IteratorAggregate
{
    /**
     * Configurators indexed by project type and tool.
     *
     * @var Configurator[][]
     */
    private $registeredConfigurators = [];

    /**
     * @param Configurator $configurator
     * @param ProjectType $projectType
     * @return void
     */
    public function registerFor(Configurator $configurator, ProjectType $projectType)
    {
        $projectTypeKey = $projectType->getProjectType();

        if (!key_exists($projectTypeKey, $this->registeredConfigurators)) {
            $this->registeredConfigurators[$projectTypeKey] = [];
        }

        $toolClassName = $configurator->getToolClassName();
        if (array_key_exists($toolClassName, $this->registeredConfigurators[$projectTypeKey])) {
            throw new LogicException(
                sprintf(
                    'Cannot register Configurator "%s" under ProjectType "%s"; ' .
                    'Configurator "%s" has already been registered for the same tool ("%s")',
                    get_class($configurator),
                    $projectType->getProjectType(),
                    get_class($this->registeredConfigurators[$projectTypeKey][$toolClassName]),
                    $toolClassName
                )
            );
        }

        $this->registeredConfigurators[$projectTypeKey][$toolClassName] = $configurator;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->registeredConfigurators);
    }

    /**
     * @param Project $project
     * @return ConfiguratorList
     */
    public function getRunListForProject(Project $project)
    {
        $configurators = new ConfiguratorList([]);

        foreach ($project->getProjectTypes() as $projectType) {
            if (isset($this->registeredConfigurators[$projectType->getProjectType()])) {
                $configurators = $configurators->appendList(
                    new ConfiguratorList($this->registeredConfigurators[$projectType->getProjectType()])
                );
            }
        }

        return $configurators;
    }
}
