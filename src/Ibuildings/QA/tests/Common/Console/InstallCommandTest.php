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

use Ibuildings\QA\Tools\Common\Application;
use Ibuildings\QA\Tools\Common\Console\InstallCommand;
use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InstallCommandTest
 *
 * @package Ibuildings\QA\tests\Common\Console
 */
class InstallCommandTest extends \PHPUnit_Framework_TestCase
{

    protected $installCommand;

    protected $application;

    public function setup()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $this->application = new Application('ibuildings qa tools', '1.1.11', $settings);

        $this->application->add(new \Ibuildings\QA\Tools\Common\Console\InstallCommand());
        $this->application->add(new \Ibuildings\QA\Tools\Javascript\Console\InstallJsHintCommand());
        $this->application->add(new \Ibuildings\QA\Tools\Common\Console\InstallPreCommitHookCommand());
        $this->application->add(new \Ibuildings\QA\Tools\Common\Console\ChangeSetPreCommitCommand());
        $this->application->add(new \Ibuildings\QA\Tools\Common\Console\RunCommand());
    }

    /**
     * @test
     */
    public function cancelOverwriteBuildConfig()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install');
        // We mock the DialogHelper
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askConfirmation'));
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(false));

        $command->getHelperSet()->set($dialog, 'dialog');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $trimmedString = trim($commandTester->getDisplay());
        $this->assertEquals('Starting setup of Ibuildings QA Tools', $trimmedString);
    }

    /**
     * @test
     */
    public function noQaToolsSelected()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install');

        // We mock the DialogHelper
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate'));

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        $dialog->expects($this->any())->method('askConfirmation')->will($this->returnValue(false));
        // We override the standard helper with our mock
        $command->getHelperSet()->set($dialog, 'dialog');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertContains('No QA tools enabled. No configuration written', $commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function antNotFound()
    {
        $checker = $this->getMock('Ibuildings\QA\Tools\Common\CommandExistenceChecker', array('commandExists'));
        $checker->expects($this->any())->method('commandExists')->will($this->returnValue(false));

        $installCommand = new \Ibuildings\QA\tests\mock\InstallCommand();
        $installCommand->setChecker($checker);

        $this->application->add($installCommand);

        /** @var InstallCommand $command */
        $command = $this->application->find('install');

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
    public function install()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install');

        // We mock the DialogHelper
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate'));

        //If you already have a build config, it will be overwritten. Do you want to continue? [Y/n]
        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->will($this->returnValue(true));

        //What is the name of the project?
        $dialog->expects($this->at(1))->method('askAndValidate')->will($this->returnValue('test1'));

        //Where do you want to store the build artifacts?
        $dialog->expects($this->at(2))->method('askAndValidate')->will($this->returnValue('test2'));

        // Are you sure? The path doesn't exist and will be created
        $dialog->expects($this->at(3))->method('askConfirmation')->will($this->returnValue(true));

        //Do you want to install the QA tools for PHP?
        $dialog->expects($this->at(4))->method('askConfirmation')->will($this->returnValue(true));

        //Do you want to run `./composer.phar install` on every commit? [y/N]
        $dialog->expects($this->at(5))->method('askConfirmation')->will($this->returnValue(true));

        //Do you want to enable PHP Lint?
        $dialog->expects($this->at(6))->method('askConfirmation')->will($this->returnValue(true));

        //Do you want to enable the PHP Mess Detector? [Y/n]
        $dialog->expects($this->at(7))->method('askConfirmation')->will($this->returnValue(true));

        //- Do you want to exclude custom patterns for PHP Mess Detector
        $dialog->expects($this->at(8))->method('askConfirmation')->will($this->returnValue(false));

        //- Do you want to enable the PHP Code Sniffer?
        $dialog->expects($this->at(9))->method('askConfirmation')->will($this->returnValue(true));

        //Which coding standard do you want to use? (PEAR, PHPCS, PSR1, PSR2, Squiz, Zend)
        $dialog->expects($this->at(10))->method('askAndValidate')->will($this->returnValue('PSR2'));

        //-- Do you want to exclude some default Symfony patterns for PHP Code Sniffer? [y/N]
        $dialog->expects($this->at(11))->method('askConfirmation')->will($this->returnValue(true));

        //- Do you want to exclude some custom patterns for PHP Code Sniffer? [y/N]
        $dialog->expects($this->at(12))->method('askConfirmation')->will($this->returnValue(false));

        //Do you want to enable PHP Copy Paste Detection? [Y/n]
        $dialog->expects($this->at(13))->method('askConfirmation')->will($this->returnValue(true));

        //Do you want to exclude patterns for PHP Copy Paste detection? [Y/n]
        $dialog->expects($this->at(14))->method('askConfirmation')->will($this->returnValue(false));

        //Do you want to enable the Sensiolabs Security Checker? [Y/n]
        $dialog->expects($this->at(15))->method('askConfirmation')->will($this->returnValue(true));

        //What is the path to the PHP source code? [src]
        $dialog->expects($this->at(16))->method('askAndValidate')->will($this->returnValue('/tmp'));

        //Do you want to enable PHPunit tests? [Y/n]
        $dialog->expects($this->at(17))->method('askConfirmation')->will($this->returnValue(true));

        //Do you have a custom PHPUnit config? (for example, Symfony has one in 'app/phpunit.xml.dist') [y/N]
        $dialog->expects($this->at(18))->method('askConfirmation')->will($this->returnValue(false));

        //What is the path to the PHPUnit tests?
        $dialog->expects($this->at(19))->method('askAndValidate')->will($this->returnValue('/tmp'));

        //Do you want to enable an autoload script for PHPUnit?
        $dialog->expects($this->at(20))->method('askConfirmation')->will($this->returnValue(true));

        //what is the path to the autoload script for PHPUnit?
        $dialog->expects($this->at(21))->method('askAndValidate')->will($this->returnValue('/tmp'));

        //Do you want to install the QA tools for Javascript? [Y/n]
        $dialog->expects($this->at(22))->method('askConfirmation')->will($this->returnValue(true));

        //Do you want to enable JSHint? [Y/n]
        $dialog->expects($this->at(23))->method('askConfirmation')->will($this->returnValue(false));

        //Do you want to install the Behat framework?
        $dialog->expects($this->at(24))->method('askConfirmation')->will($this->returnValue(true));

        //What is base url of the ci environment?
        $dialog->expects($this->at(25))->method('askAndValidate')->will($this->returnValue('http://test'));

        //What is base url of the ci environment?
        $dialog->expects($this->at(26))->method('askAndValidate')->will($this->returnValue('http://ci.test'));

        //Do you want to enable the git pre-commit hook? It will run the QA tools on every commit
        $dialog->expects($this->at(27))->method('askConfirmation')->will($this->returnValue(true));

        // We override the standard helper with our mock
        $command->getHelperSet()->set($dialog, 'dialog');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $display = $commandTester->getDisplay();
        $this->assertContains('Config file for PHP Mess Detector written', $display);
        $this->assertContains('Config file for PHP Code Sniffer written', $display);
        $this->assertContains('Config file for PHPUnit written', $display);
        $this->assertContains('Ant build file written', $display);
        $this->assertContains('Ant pre commit build file written', $display);
    }
}
