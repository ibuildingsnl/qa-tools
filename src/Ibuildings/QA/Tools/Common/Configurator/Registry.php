<?php
namespace Ibuildings\QA\Tools\Common\Configurator;

/**
 * @todo give this class a better name
 */
class Registry
{
    /**
     * @var array
     */
    private $configurators;

    /**
     * @param ConfiguratorInterface $configurator
     */
    public function register(ConfiguratorInterface $configurator)
    {
        $this->configurators[get_class($configurator)] = $configurator;
    }

    /**
     * Executes all configurators
     */
    public function executeConfigurators()
    {
        /**
         * @var ConfiguratorInterface
         */
        foreach ($this->configurators as $configurator) {
            $configurator->configure();
        }
    }
}