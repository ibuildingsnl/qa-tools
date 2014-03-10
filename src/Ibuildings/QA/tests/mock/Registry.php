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
        $expectMultiPath = array('PhpMessDetectorConfigurator', 'PhpCodeSnifferConfigurator');

        if (is_a($configurator, 'Ibuildings\QA\Tools\Common\Configurator\AbstractWritableConfigurator')) {
            $className = end(explode('\\', get_class($configurator)));
            $mockClass = "Ibuildings\\QA\\tests\\mock\\Configurator\\" . $className;
            if (class_exists($mockClass)) {
                if (in_array($className, $expectMultiPath)) {
                    $multiplePathHelper = new MultiplePathHelper(
                        $this->outputInterface,
                        $this->dialogHelper,
                        $this->settings->getBaseDir()
                    );

                    $this->configurators[get_class($configurator)] = new $mockClass(
                        $this->outputInterface,
                        $this->dialogHelper,
                        $multiplePathHelper,
                        $this->settings,
                        $this->twig
                    );
                } elseif ($className === 'JsHintConfigurator') {
                    $this->configurators[get_class($configurator)] = new $mockClass(
                        $this->inputInterface,
                        $this->outputInterface,
                        $this->dialogHelper,
                        $this->settings,
                        $this->twig,
                        $this->installJsHintCommand
                    );
                } else {
                    $this->configurators[get_class($configurator)] = new $mockClass(
                        $this->outputInterface,
                        $this->dialogHelper,
                        $this->settings,
                        $this->twig
                    );
                }

                return;
            }
        } else {
            parent::register($configurator);
        }
    }

    public function getConfiguratorByName($name) {
        return $this->configurators[$name];
    }

}
