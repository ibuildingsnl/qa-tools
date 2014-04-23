<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Configurator;

/**
 * @todo give this class a better name
 */
class Registry
{
    /**
     * @var array
     */
    protected $configurators;

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

        $this->writeConfigurationFiles();
    }

    /**
     * Writes config
     */
    private function writeConfigurationFiles()
    {
        foreach ($this->configurators as $configurator) {
            if (!$configurator instanceof ConfigurationWriterInterface) {
                continue;
            }

            if (!$configurator->shouldWrite()) {
                continue;
            }

            $configurator->writeConfig();
        }
    }
}
