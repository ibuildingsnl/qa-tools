<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\test\PHP\Configurator;

use Ibuildings\QA\tests\mock\SettingsMock;
use Ibuildings\QA\Tools\Common\Configurator\Helper\MultiplePathHelper;
use Ibuildings\QA\Tools\PHP\Configurator\PhpSourcePathConfigurator;
use \PHPUnit_Framework_TestCase as UnitTest;

/**
 * Class PhpSourceConfiguratorTest
 * @package Ibuildings\QA\test\PHP\Configurator
 */
class PhpSourceConfiguratorTest extends Unittest
{
    /**
     * @var \Ibuildings\QA\tests\mock\SettingsMock
     */
    private $settings;

    /**
     * @var \Ibuildings\QA\Tools\PHP\Configurator\PhpSourcePathConfigurator
     */
    private $configurator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     *
     * mock of \Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper
     */
    private $dialog;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     *
     * mock of \Symfony\Component\Console\Output\ConsoleOutput
     */
    private $output;

    public function setUp()
    {
        $this->dialog = $this->getMock(
            'Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper',
            array('askConfirmation', 'askAndValidate')
        );

        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $this->settings = new SettingsMock($baseDir, $baseDir);

        $this->output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutput');
        $this->configurator = new PhpSourcePathConfigurator(
            $this->output,
            $this->dialog,
            new MultiplePathHelper($this->output, $this->dialog, $baseDir, $this->settings),
            $this->settings
        );
    }

    /**
     * @test
     *
     * @dataProvider isInActiveProvider
     */
    public function assertIsNotActiveWhenNotRequired(array $settings)
    {
        $this->settings->exchangeArray($settings);

        $this->configurator->configure();

        $this->assertEquals($settings, $this->settings->getArrayCopy());
    }

    /**
     * @test
     *
     * @dataProvider isActiveProvider
     */
    public function assertIsActiveIfRequired(array $settings)
    {
        $this->settings->exchangeArray($settings);

        $this->dialog
            ->expects($this->at(0))
            ->method('askAndValidate')
            ->with(
                $this->equalTo($this->output),
                $this->equalTo("At which paths is the PHP source code located? [src] (comma separated)\n")
            )
            ->will($this->returnValue(array('src')));

        $this->configurator->configure();

        $settings['phpSrcPath'] =  array('src');
        $this->assertEquals(
            $settings,
            $this->settings->getArrayCopy()
        );
    }

    /**
     * @test
     */
    public function assertThatReturnValueWorksWithMultiplePaths()
    {
        $settings = array(
            'enablePhpTools' => true,
            'enablePhpMessDetector' => 'true'
        );
        $this->settings->exchangeArray($settings);

        $this->dialog
            ->expects($this->at(0))
            ->method('askAndValidate')
            ->with(
                $this->equalTo($this->output),
                $this->equalTo("At which paths is the PHP source code located? [src] (comma separated)\n")
            )
            ->will($this->returnValue(array('src', 'foo')));

        $this->configurator->configure();

        $settings['phpSrcPath'] = array('src', 'foo');
        $this->assertEquals(
            $settings,
            $this->settings->getArrayCopy()
        );
    }

    /**
     * @return array
     */
    public function isInActiveProvider()
    {
        return array(
            'no php tools' => array(
                array(
                    'enablePhpTools' => false
                )
            ),
            'PHP tools enabled, but not required by tools' => array(
                array(
                    'enablePhpTools' => true,
                    'enablePhpMessDetector' => false,
                    'enablePhpCodeSniffer' => false,
                    'enablePhpCopyPasteDetection' => false,
                    'enablePhpLint' => false
                )
            )
        );
    }

    /**
     * @return array
     */
    public function isActiveProvider()
    {
        return array(
            'only phpMessDetector' => array(
                array(
                    'enablePhpTools' => true,
                    'enablePhpMessDetector' => true,
                    'enablePhpCodeSniffer' => false,
                    'enablePhpCopyPasteDetection' => false,
                    'enablePhpLint' => false
                )
            ),
            'only CodeSniffer' => array(
                array(
                    'enablePhpTools' => true,
                    'enablePhpMessDetector' => false,
                    'enablePhpCodeSniffer' => true,
                    'enablePhpCopyPasteDetection' => false,
                    'enablePhpLint' => false
                )
            ),
            'only CPD' => array(
                array(
                    'enablePhpTools' => true,
                    'enablePhpMessDetector' => false,
                    'enablePhpCodeSniffer' => false,
                    'enablePhpCopyPasteDetection' => true,
                    'enablePhpLint' => false
                )
            ),
            'only lint' => array(
                array(
                    'enablePhpTools' => true,
                    'enablePhpMessDetector' => false,
                    'enablePhpCodeSniffer' => false,
                    'enablePhpCopyPasteDetection' => false,
                    'enablePhpLint' => true
                )
            ),
            'some tools' => array(
                array(
                    'enablePhpTools' => true,
                    'enablePhpMessDetector' => false,
                    'enablePhpCodeSniffer' => true,
                    'enablePhpCopyPasteDetection' => true,
                    'enablePhpLint' => true
                )
            )
        );
    }
}
