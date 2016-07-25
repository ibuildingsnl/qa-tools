<?php

namespace Ibuildings\QaTools\Core\Configurator;

use ArrayIterator;
use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Project\ProjectType;
use IteratorAggregate;

final class ConfiguratorRegistry implements IteratorAggregate
{
    /**
     * @var ConfiguratorList[]
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
            $this->registeredConfigurators[$projectTypeKey] = new ConfiguratorList([$configurator]);
            return;
        }

        if ($this->registeredConfigurators[$projectTypeKey]->contains($configurator)) {
            throw new LogicException(sprintf(
                'Cannot register Configurator "%" for ProjectType "%s" with ConfiguratorRegistry; ' .
                'it has already been registered (%s)',
                get_class($configurator),
                $projectType->getProjectType(),
                implode(', ', $this->registeredConfigurators)
            ));
        }

        $this->registeredConfigurators[$projectTypeKey] =
            $this->registeredConfigurators[$projectTypeKey]->append($configurator);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->registeredConfigurators);
    }

    /**
     * @param ProjectType[] $projectTypes
     * @return ConfiguratorList
     */
    public function getRunListForProjectTypes(array $projectTypes)
    {
        Assertion::allIsInstanceOf($projectTypes, ProjectType::class);

        $configurators = new ConfiguratorList([]);

        /** @var ProjectType $projectType */
        foreach ($projectTypes as $projectType) {
            if (isset($this->registeredConfigurators[$projectType->getProjectType()])) {
                $configurators = $configurators->appendList(
                    $this->registeredConfigurators[$projectType->getProjectType()]
                );
            }
        }

        return $configurators;
    }
}
