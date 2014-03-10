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
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
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
        $this->application->add(new \Ibuildings\QA\tests\mock\InstallPreCommitHookCommand());
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


        $this->addBaseExpects($dialog);
        $this->addQAExpects($dialog);
        $this->addPHPMDExpects($dialog);
        $this->addPHPCSExpects($dialog);
        $this->addCodeDuplicateExpects($dialog);
        $this->addPhpUnitExpects($dialog);
        $this->addFinishingExpects($dialog);

        // We override the standard helper with our mock
        $command->getHelperSet()->set($dialog, 'dialog');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array('verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE));

        $display = $commandTester->getDisplay();
        $this->assertContains('Config file for PHP Mess Detector written', $display);
        $this->assertContains('Config file for PHP Code Sniffer written', $display);
        $this->assertContains('Config file for PHPUnit written', $display);
        $this->assertContains('Ant build file written', $display);
        $this->assertContains('Ant pre commit build file written', $display);
    }

    /**
     * @test
     */
    public function installWithCustomPhpUnit()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install');

        // We mock the DialogHelper
        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate'));

        $this->addBaseExpects($dialog);
        $this->addQAExpects($dialog);
        $this->addPHPMDExpects($dialog);
        $this->addPHPCSExpects($dialog);
        $this->addCodeDuplicateExpects($dialog);
        $this->addPhpUnitWithCustomPathExpects($dialog);
        $this->addFinishingExpects($dialog, 19);

        // We override the standard helper with our mock
        $command->getHelperSet()->set($dialog, 'dialog');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $display = $commandTester->getDisplay();
        $this->assertContains('Config file for PHP Mess Detector written', $display);
        $this->assertContains('Config file for PHP Code Sniffer written', $display);
        $this->assertNotContains('Config file for PHPUnit written', $display);
        $this->assertContains('Ant build file written', $display);
        $this->assertContains('Ant pre commit build file written', $display);
    }

    /**
     * Will add the base start expects to the dialog helper
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addBaseExpects(DialogHelper $dialog, $startAt = 0)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo(
                    "\n<comment>If you already have a build config, it will be overwritten. Do you want to continue? [Y/n] </comment>"
                )
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo('What is the name of the project? [Qa Tools] ')
            )
            ->will($this->returnValue('test1'));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo('Where do you want to store the build artifacts? [build/artifacts] ')
            )
            ->will($this->returnValue('/test2/etstter'));
    }

    /**
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addQAExpects(DialogHelper $dialog, $startAt = 3)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\n<comment>Do you want to install the QA tools for PHP? [Y/n] </comment>")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to run `./composer.phar install` on every commit? [y/N] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable PHP Lint? [Y/n] ')
            )
            ->will($this->returnValue(true));
    }

    /**
     * Adds answers to the PHPMD questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPHPMDExpects(DialogHelper $dialog, $startAt = 6)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable the PHP Mess Detector? [Y/n] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('  - Do you want to exclude custom patterns for PHP Mess Detector? [y/N] ')
            )
            ->will($this->returnValue(false));
    }

    /**
     * Adds answers to the PHPCS questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPHPCSExpects(DialogHelper $dialog, $startAt = 8)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable the PHP Code Sniffer? [Y/n] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo(
                    '  - Which coding standard do you want to use? (PEAR, PHPCS, PSR1, PSR2, Squiz, Zend) [PSR2] '
                )
            )
            ->will($this->returnValue('PSR2'));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('  - Do you want to exclude some default Symfony patterns for PHP Code Sniffer? [y/N] ')
            )
            ->will($this->returnValue(true));

        //- Do you want to exclude some custom patterns for PHP Code Sniffer? [y/N]
        $dialog
            ->expects($this->at($startAt))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('  - Do you want to exclude some custom patterns for PHP Code Sniffer? [y/N] ')
            )
            ->will($this->returnValue(false));
    }

    /**
     * Add answers to the duplicate code questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt
     */
    protected function addCodeDuplicateExpects(DialogHelper $dialog, $startAt = 12)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to enable PHP Copy Paste Detection? [Y/n] ")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to exclude patterns for PHP Copy Paste detection? [Y/n] ")
            )
            ->will($this->returnValue(false));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to enable the Sensiolabs Security Checker? [Y/n] ")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("What is the path to the PHP source code? [src] ")
            )
            ->will($this->returnValue('/tmp'));
    }

    /**
     * Adds answers to the phpunit questions.
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPhpUnitExpects(DialogHelper $dialog, $startAt = 16)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable PHPunit tests? [Y/n] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you have a custom PHPUnit config? (for example, Symfony has one in \'app/phpunit.xml.dist\') [y/N] ')
            )
            ->will($this->returnValue(false));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo('What is the path to the PHPUnit tests? [tests] ')
            )
            ->will($this->returnValue('/tmp'));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable an autoload script for PHPUnit? [Y/n] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo('What is the path to the autoload script for PHPUnit? [vendor/autoload.php] ')
            )
            ->will($this->returnValue('/tmp'));
    }

    /**
     * Add alternative answers the phpunit questions, in this case we already have a phpunit config so we get different
     * questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPhpUnitWithCustomPathExpects(DialogHelper $dialog, $startAt = 16)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable PHPunit tests? [Y/n] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you have a custom PHPUnit config? (for example, Symfony has one in \'app/phpunit.xml.dist\') [y/N] ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo('What is the path to the custom PHPUnit config? [app/phpunit.xml.dist] ')
            )
            ->will($this->returnValue(__DIR__ . '/fixtures/phpunit.test'));
    }

    /**
     * Ads answers to the last questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addFinishingExpects(DialogHelper $dialog, $startAt = 21)
    {
        //Do you want to install the QA tools for Javascript? [Y/n]
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\n<comment>Do you want to install the QA tools for Javascript? [Y/n] </comment>")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable JSHint? [Y/n] ')
            )
            ->will($this->returnValue(false));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\n<comment>Do you want to install the Behat framework? [Y/n] </comment>")
            )
            ->will($this->returnValue(true));


        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("What is base url of your application? [http://www.ibuildings.nl] ")
            )
            ->will($this->returnValue('http://test'));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("What is base url of the ci environment? [http://ci.test] ")
            )
            ->will($this->returnValue('http://ci.test'));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("What is base url of your dev environment? [http://dev.test] ")
            )
            ->will($this->returnValue('http://dev.test'));

        $dialog
            ->expects($this->at($startAt))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo(
                    "\n<comment>Do you want to enable the git pre-commit hook? It will run the QA tools on every commit [Y/n] </comment>"
                )
            )
            ->will($this->returnValue(true));
    }
}
