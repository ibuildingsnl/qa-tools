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

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BehatConfigurator
 *
 * mock class of configurator to be able to catch the writing to file and add it to a internal var so we can test
 * the contents of the string without creating random files
 *
 * @package Ibuildings\QA\tests\mock\Configurator
 */
class BehatConfigurator extends \Ibuildings\QA\Tools\Functional\Configurator\BehatConfigurator
{

    /**
     * @var string
     */
    public $behatOutput;

    /**
     * @var string
     */
    public $behatDevOutput;
    /**
     * Only writes the content to the file system so don't do that
     * @return null|void
     */
    protected function writeBehatYamlFiles()
    {
        $this->behatOutput = $this->twig->render('behat.yml.dist', $this->settings->getArrayCopy());
        $this->behatDevOutput =  $this->twig->render('behat.dev.yml.dist', $this->settings->getArrayCopy());
        return null;
    }

    /**
     * effectively we don't do anything here but copy files around so just return null as we don't want this for tests
     *
     * @param OutputInterface $output
     *
     * @return null|void
     */
    protected function writeBehatExamples(OutputInterface $output)
    {
        return null;
    }
}
