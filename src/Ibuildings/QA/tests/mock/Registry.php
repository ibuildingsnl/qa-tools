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

use Ibuildings\QA\Tools\Common\Configurator\ConfigurationWriterInterface;
use Ibuildings\QA\Tools\Common\Configurator\ConfiguratorInterface;
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\Javascript\Console\InstallJsHintCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Registry
 *
 * This mock class is intented to catch certain special configurations and create mock classes for them so we can
 * make sure we don;t write to the file system for those configurations
 *
 * @package Ibuildings\QA\tests\mock
 */
class Registry extends \Ibuildings\QA\Tools\Common\Configurator\Registry
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $inputInterface;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $outputInterface;

    /**
     * @var \Symfony\Component\Console\Helper\DialogHelper
     */
    protected $dialogHelper;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    protected $installJsHintCommand;

    /**
     * @param InputInterface       $inputInterface
     * @param OutputInterface      $outputInterface
     * @param DialogHelper         $dialogHelper
     * @param Settings             $settings
     * @param \Twig_Environment    $twig
     * @param InstallJsHintCommand $installJsHintCommand
     */
    public function __construct(
        InputInterface $inputInterface,
        OutputInterface $outputInterface,
        DialogHelper $dialogHelper,
        Settings $settings,
        \Twig_Environment $twig,
        InstallJsHintCommand $installJsHintCommand
    )
    {
        $this->inputInterface = $inputInterface;
        $this->outputInterface = $outputInterface;
        $this->dialogHelper = $dialogHelper;
        $this->settings = $settings;
        $this->twig = $twig;
        $this->installJsHintCommand = $installJsHintCommand;
    }

    /**
     * @param ConfiguratorInterface $configurator
     */
    public function register(ConfiguratorInterface $configurator)
    {
        if (!$configurator instanceof ConfigurationWriterInterface) {
            parent::register($configurator);
            return;
        }

        // This really ought to be done differently... with proper DI and consistent usage of SF/Filesystem component
        // we could just write to tmp dirs and test it properly (see FilesystemTestCase in File component)
        $className = explode('\\', get_class($configurator));
        $className = end($className);
        $mockClass = "Ibuildings\\QA\\tests\\mock\\Configurator\\" . $className;
        if (!class_exists($mockClass)) {
            throw new \RuntimeException(sprintf(
                'Configurator "%s" implements ConfigurationWriterInterface. A mock of this configurator should be'
                . 'made so that the writeConfig method does not write to disk but stores the contents',
                get_class($configurator)
            ));
        }

        if (in_array($className, array('PhpMessDetectorConfigurator', 'PhpCodeSnifferConfigurator'))) {
            $multiplePathHelper = new MultiplePathHelper(
                $this->outputInterface,
                $this->dialogHelper,
                $this->settings->getBaseDir()
            );

            $writingMockedConfigurator = new $mockClass(
                $this->outputInterface,
                $this->dialogHelper,
                $multiplePathHelper,
                $this->settings,
                $this->twig
            );
        } elseif ($className === 'JsHintConfigurator') {
            $writingMockedConfigurator = new $mockClass(
                $this->inputInterface,
                $this->outputInterface,
                $this->dialogHelper,
                $this->settings,
                $this->twig,
                $this->installJsHintCommand
            );
        } else {
            $writingMockedConfigurator = new $mockClass(
                $this->outputInterface,
                $this->dialogHelper,
                $this->settings,
                $this->twig
            );
        }

        // should be resolved asap
        $this->configurators[get_class($configurator)] = $writingMockedConfigurator;
    }

    public function getConfiguratorByName($name)
    {
        return $this->configurators[$name];
    }

}
