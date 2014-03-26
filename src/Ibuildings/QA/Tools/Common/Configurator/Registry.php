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

        $this->writeConfig();
    }

    /**
     * Writes config
     */
    private function writeConfig()
    {
        /**
         * @var ConfiguratorInterface
         */
        foreach ($this->configurators as $configurator) {
            if (method_exists($configurator, 'writeConfig')) {
                $configurator->writeConfig();
            }
        }
    }
}
