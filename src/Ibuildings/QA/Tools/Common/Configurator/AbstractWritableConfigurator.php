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
 * Class AbstractWritableConfigurator
 *
 * @package Ibuildings\QA\Tools\Common\Configurator
 */
abstract class AbstractWritableConfigurator implements ConfiguratorInterface
{
    /**
     * Writes the config to a file
     *
     * @codeCoverageIgnore
     */
    abstract public function writeConfig();

    /**
     * checks if a file should be written to the filesystem or not
     *
     * @return boolean
     */
    abstract public function shouldWrite();
}
