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
use Ibuildings\QA\Tools\Common\Console\AbstractCommand;
use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InstallPreCommitHookCommandTest
 *
 * @package Ibuildings\QA\tests\Common\Console
 */
class InstallPreCommitHookCommandTest extends \PHPUnit_Framework_TestCase
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

        $preCommitHookCommand = new InstallPreCommitHookCommand();
        $preCommitHookCommand->setChecker($checker);

        $this->application = $this->getMock('Ibuildings\QA\Tools\Common\Application', array('getDialogHelper'), array('ibuildings qa tools', '1.1.11', $settings));

        $dialog = $this->getMock('Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate'));

        $this->application->expects($this->any())
            ->method('getDialogHelper')
            ->will($this->returnValue($dialog));

        $this->application->add($preCommitHookCommand);
    }

    /**
     * @test
     */
    public function antNotFound()
    {
        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(false));

        $preCommitHookCommand = new InstallPreCommitHookCommand();
        $preCommitHookCommand->setChecker($checker);

        $this->application->add($preCommitHookCommand);

        /** @var InstallPreCommitHookCommand $command */
        $command = $this->application->find('install:pre-commit');

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
    public function md5HashNotFound()
    {
        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker
            ->expects($this->at(0))
            ->method('commandExists')
            ->with(
                $this->equalTo('ant -version'),
                $this->equalTo(null),
                $this->equalTo(AbstractCommand::MINIMAL_VERSION_ANT),
                $this->equalTo(null)
            )
            ->will($this->returnValue(true));

        $checker
            ->expects($this->at(1))
            ->method('commandExists')
            ->with($this->equalTo(array('md5', 'md5sum')))
            ->will($this->returnValue(false));

        $preCommitHookCommand = new InstallPreCommitHookCommand();
        $preCommitHookCommand->setChecker($checker);

        $this->application->add($preCommitHookCommand);

        $dialog = $this->application->getDialogHelper();

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        /** @var InstallPreCommitHookCommand $command */
        $command = $this->application->find('install:pre-commit');

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
    public function gitNotFound()
    {
        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker
            ->expects($this->at(0))
            ->method('commandExists')
            ->with(
                $this->equalTo('ant -version'),
                $this->equalTo(null),
                $this->equalTo(AbstractCommand::MINIMAL_VERSION_ANT),
                $this->equalTo(null)
            )
            ->will($this->returnValue(true));

        $checker
            ->expects($this->at(1))
            ->method('commandExists')
            ->with($this->equalTo(array('md5', 'md5sum')))
            ->will($this->returnValue(true));

        $checker
            ->expects($this->at(2))
            ->method('commandExists')
            ->with(
                $this->equalTo('git'),
                $this->equalTo(null),
                $this->equalTo(AbstractCommand::MINIMAL_VERSION_GIT),
                $this->equalTo(null)
            )
            ->will($this->returnValue(false));


        $preCommitHookCommand = new InstallPreCommitHookCommand();
        $preCommitHookCommand->setChecker($checker);

        $this->application->add($preCommitHookCommand);

        $dialog = $this->application->getDialogHelper();

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        /** @var InstallPreCommitHookCommand $command */
        $command = $this->application->find('install:pre-commit');

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
    public function everythingFound()
    {
        $dialog = $this->application->getDialogHelper();

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        /** @var InstallPreCommitHookCommand $command */
        $command = $this->application->find('install:pre-commit');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array(
                'command' => $command->getName()
            )
        );

        $this->assertStringEqualsFile(__DIR__ . '/fixtures/precommithook', $command->precommitHookContent);
    }

    /**
     * @test
     */
    public function dotGitDirectoryNotFound()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(true));

        $preCommitHookCommand = $this->getMock(
            'Ibuildings\QA\tests\mock\InstallPreCommitHookCommand',
            array('gitHooksDirExists')
        );

        $preCommitHookCommand->setChecker($checker);
        $preCommitHookCommand->expects($this->any())->method('gitHooksDirExists')->will($this->returnValue(false));

        $application = $this->getMock('Ibuildings\QA\Tools\Common\Application', array('getDialogHelper'), array('ibuildings qa tools', '1.1.11', $settings));

        $dialog = $this->getMock('Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate'));

        $application->expects($this->any())
            ->method('getDialogHelper')
            ->will($this->returnValue($dialog));

        $application->add($preCommitHookCommand);

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\nDo you want to enable the git pre-commit hook? It will run the QA tools on every commit")
            )
            ->will($this->returnValue(true));

        /** @var InstallPreCommitHookCommand $command */
        $command = $application->find('install:pre-commit');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array(
                'command' => $command->getName()
            )
        );

        $display = $commandTester->getDisplay();

        $this->assertContains(
            'You don\'t have an initialized git repo or hooks directory. Not setting pre-commit hook.',
            $display
        );
    }

    /**
     * @test
     */
    public function commitHookExists()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(true));

        $preCommitHookCommand = $this->getMock(
            'Ibuildings\QA\tests\mock\InstallPreCommitHookCommand',
            array('preCommitHookExists')
        );

        $preCommitHookCommand->setChecker($checker);
        $preCommitHookCommand->expects($this->any())->method('preCommitHookExists')->will($this->returnValue(true));

        $application = $this->getMock('Ibuildings\QA\Tools\Common\Application', array('getDialogHelper'), array('ibuildings qa tools', '1.1.11', $settings));

        $dialog = $this->getMock('Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate'));

        $application->expects($this->any())
            ->method('getDialogHelper')
            ->will($this->returnValue($dialog));

        $application->add($preCommitHookCommand);

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        /** @var InstallPreCommitHookCommand $command */
        $command = $application->find('install:pre-commit');

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array(
                'command' => $command->getName()
            )
        );

        $display = $commandTester->getDisplay();

        $this->assertContains(
            'You already have a git pre-commit hook',
            $display
        );
    }
}
