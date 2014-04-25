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

use Ibuildings\QA\tests\mock\InstallPreCommitHookCommand;
use Ibuildings\QA\Tools\Common\Application;
use Ibuildings\QA\tests\mock\RunCommand;
use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RunCommandTest
 *
 * @package Ibuildings\QA\tests\Common\Console
 */
class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $application;

    public function setup()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(true));

        $preCommitHookCommand = new RunCommand();
        $preCommitHookCommand->setChecker($checker);

        $this->application = new Application('ibuildings qa tools', '1.1.17', $settings);

        $this->application->add($preCommitHookCommand);
    }

    /**
     * @test
     */
    public function antNotFound()
    {
        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(false));

        $runCommand = new RunCommand();
        $runCommand->setChecker($checker);

        $this->application->add($runCommand);

        /** @var InstallPreCommitHookCommand $command */
        $command = $this->application->find('run');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array(
                'command' => $command->getName()
            )
        );

        $display = $commandTester->getDisplay();
        $this->assertContains('-> Exiting', $display);
    }

    /**
     * @test
     */
    public function runCommand()
    {
        /** @var InstallPreCommitHookCommand $command */
        $command = $this->application->find('run');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array(
                'command'       => $command->getName(),
                '--working-dir' => 'test'
            ),
            array('verbosity' => OutputInterface::VERBOSITY_NORMAL)
        );

        $display = $commandTester->getDisplay();

        $this->assertNotContains('-> Exiting', $display);
    }
}
