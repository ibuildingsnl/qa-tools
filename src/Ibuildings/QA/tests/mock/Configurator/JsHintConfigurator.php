<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\tests\mock\Configurator;

/**
 * Class JsHintConfigurator
 *
 * mock class of configurator to be able to catch the writing to file and add it to a internal var so we can test
 * the contents of the string without creating random files
 *
 * @package Ibuildings\QA\tests\mock\Configurator
 */
class JsHintConfigurator extends \Ibuildings\QA\Tools\Javascript\Configurator\JsHintConfigurator
{
    /**
     * @var string
     */
    public $outputString;

    /**
     * @inheritdoc
     */
    public function writeConfig()
    {
        $this->outputString = $this->twig->render('.jshintrc.dist', $this->settings->getArrayCopy());
        $this->output->writeln("\n<info>Config file for JSHint written</info>");
    }
}
