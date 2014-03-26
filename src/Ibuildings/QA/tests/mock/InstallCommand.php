<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\tests\mock;

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallCommand
 *
 * @package Ibuildings\QA\tests\mock
 */
class InstallCommand extends \Ibuildings\QA\Tools\Common\Console\InstallCommand
{
    /**
     * @var OutputInterface
     */
    protected $catchedOutput;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var InputInterface
     */
    protected $catchedInput;
    /**
     * @var CommandExistenceChecker
     */
    protected $checker;

    /**
     * @var string
     */
    public $buildXmlOutput;

    /**
     * @var string
     */
    public $buildPreCommitXmlOutput;

    /**
     * @param \Ibuildings\QA\Tools\Common\CommandExistenceChecker $checker
     */
    public function setChecker($checker)
    {
        $this->checker = $checker;
    }

    /**
     * Overwrite to be able to use a mock commandExistence checker
     *
     * @return CommandExistenceChecker
     */
    protected function getCommandExistenceChecker()
    {
        if (isset($this->checker)) {
            return $this->checker;
        }

        return new CommandExistenceChecker();
    }

    /**
     * Will give access to the configuration registry so we can check the output of
     * the configurators
     *
     * @return Registry|\Ibuildings\QA\Tools\Common\Configurator\Registry
     */
    public function getConfiguratorRegistry()
    {
        $installJsHintCommand = $this->getApplication()->find('install:jshint');

        if (is_null($this->registry)) {
            $this->registry = new Registry(
                $this->catchedInput,
                $this->catchedOutput,
                $this->dialog,
                $this->settings,
                $this->twig,
                $installJsHintCommand
            );
        }

        return $this->registry;
    }

    /**
     * Overwitten to be able to catch the input and output to use in creating of the mock configurators
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->catchedOutput = $output;
        $this->catchedInput = $input;

        parent::execute($input, $output);
    }

    protected function writeRenderedContentTo($toFile, $templateName, $params)
    {
        $content = $this->twig->render(
            $templateName,
            $params
        );

        if ($templateName === 'build-pre-commit.xml.dist') {
            $this->buildPreCommitXmlOutput = $content;
        } else {
            $this->buildXmlOutput = $content;
        }
    }
}
