<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\tests\Common\Console;

use Ibuildings\QA\tests\mock\InstallJsHintCommand;
use Ibuildings\QA\Tools\Common\Application;
use Ibuildings\QA\Tools\Common\Console\InstallCommand;
use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InstallCommandTest
 *
 * @package Ibuildings\QA\tests\Common\Console
 */
class InstallJsHintCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $application;

    public function setup()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $this->application = new Application('ibuildings qa tools', '1.1.17', $settings);

        /** @var InstallJsHintCommand $command */
        $command = $this->getMock('Ibuildings\QA\tests\mock\InstallJsHintCommand', array('installNpmDependencies'));
        $command
            ->expects($this->any())
            ->method('installNpmDependencies')
            ->will($this->returnValue(InstallJsHintCommand::CODE_SUCCESS));

        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(true));
        $command->setChecker($checker);

        $this->application->add($command);
    }

    /**
     * @test
     */
    public function install()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install:jshint');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName())
        );

        $display = $commandTester->getDisplay();
        $this->assertContains('Starting setup of the pre-commit hook for the Ibuildings QA Tools', $display);
        $this->assertNotContains('-> Not enabling JSHint', $display);
    }

    /**
     * @test
     */
    public function installFailed()
    {
        $command = $this->getMock('Ibuildings\QA\tests\mock\InstallJsHintCommand', array('installNpmDependencies'));
        $command
            ->expects($this->any())
            ->method('installNpmDependencies')
            ->will($this->returnValue(InstallJsHintCommand::CODE_ERROR));
        $this->application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName())
        );

        $display = $commandTester->getDisplay();
        $this->assertContains('Starting setup of the pre-commit hook for the Ibuildings QA Tools', $display);
        $this->assertContains('Could not install JSHint -> Not enabling JSHint', $display);
    }


    /**
     * @test
     */
    public function noNodeInstalled()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install:jshint');

        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->at(0))->method('commandExists')->will($this->returnValue(false));

        $command->setChecker($checker);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName())
        );

        $display = $commandTester->getDisplay();
        $this->assertContains('Starting setup of the pre-commit hook for the Ibuildings QA Tools', $display);
        $this->assertContains('-> Not enabling JSHint', $display);
        $this->assertNotContains('Could not install JSHint -> Not enabling JSHint', $display);
    }

    /**
     * @test
     */
    public function noNpmInstalled()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install:jshint');

        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->at(0))->method('commandExists')->will($this->returnValue(true));
        $checker->expects($this->at(1))->method('commandExists')->will($this->returnValue(false));

        $command->setChecker($checker);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName())
        );

        $display = $commandTester->getDisplay();
        $this->assertContains('Starting setup of the pre-commit hook for the Ibuildings QA Tools', $display);
        $this->assertContains('-> Not enabling JSHint', $display);
        $this->assertNotContains('Could not install JSHint -> Not enabling JSHint', $display);
    }
}
