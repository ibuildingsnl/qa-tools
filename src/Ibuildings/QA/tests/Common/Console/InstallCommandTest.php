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

use Ibuildings\QA\tests\mock\SettingsMock;
use Ibuildings\QA\Tools\Common\Console\InstallCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InstallCommandTest
 *
 * @package Ibuildings\QA\tests\Common\Console
 *
 * @ignore("PHPMD")
 */
class InstallCommandTest extends \PHPUnit_Framework_TestCase
{

    protected $installCommand;

    protected $application;

    public function setup()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new SettingsMock($baseDir, $packageBaseDir);

        $this->application = $this->getMock('Ibuildings\QA\Tools\Common\Application', array('getDialogHelper'), array('ibuildings qa tools', '1.1.17', $settings));

        $dialog = $this->getMock('Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper', array('askConfirmation', 'askAndValidate', 'select'));

        $this->application->expects($this->any())
            ->method('getDialogHelper')
            ->will($this->returnValue($dialog));

        $installCommand = new \Ibuildings\QA\tests\mock\InstallCommand();

        $this->application->add($installCommand);
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

        $dialog = $this->application->getDialogHelper();

        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\nIf you already have a build config, it will be overwritten. Do you want to continue?")
            )
            ->will($this->returnValue(false));

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
        $dialog = $this->application->getDialogHelper();

        $dialog->expects($this->at(0))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\nIf you already have a build config, it will be overwritten. Do you want to continue?")
            )
            ->will($this->returnValue(true));

        $dialog->expects($this->any())->method('askConfirmation')->will($this->returnValue(false));
        // We override the standard helper with our mock
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
        /** @var \Ibuildings\QA\tests\mock\InstallCommand $command */
        $command = $this->application->find('install');

        // We mock the DialogHelper
        $dialog = $this->application->getDialogHelper();

        $startAt = 0;
        $this->addBaseExpects($dialog, $startAt);
        $this->addBuildArtifactExpects($dialog, $startAt);
        $this->addTravisExpects($dialog, $startAt);
        $this->addQAExpects($dialog, $startAt);
        $this->addPHPMDExpects($dialog, $startAt);
        $this->addPHPCSExpects($dialog, $startAt);
        $this->addCodeDuplicateExpects($dialog, $startAt);
        $this->addPhpUnitExpects($dialog, $startAt);
        $this->addFinishingExpects($dialog, $startAt);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array('verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE));

        $display = $commandTester->getDisplay();

        $this->assertStringEqualsFile(
            __DIR__ . '/fixtures/behat.yml',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\Functional\Configurator\BehatConfigurator')->behatOutput
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/fixtures/behat.dev.yml',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\Functional\Configurator\BehatConfigurator')->behatDevOutput
        );

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/phpcs.xml',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\PHP\Configurator\PhpCodeSnifferConfigurator')->outputString
        );

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/phpmd.xml',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\PHP\Configurator\PhpMessDetectorConfigurator')->outputString
        );

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/phpmd-pre-commit.xml',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\PHP\Configurator\PhpMessDetectorConfigurator')->preCommitOutputString
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/fixtures/phpunit.test',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\PHP\Configurator\PhpUnitConfigurator')->outputString
        );


        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/build.xml',
            $command->buildXmlOutput
        );

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/build-pre-commit.xml',
            $command->buildPreCommitXmlOutput
        );


        $this->assertContains('Config file for PHP Mess Detector written', $display);
        $this->assertContains('Config file for PHP Code Sniffer written', $display);
        $this->assertContains('Config file for PHPUnit written', $display);
        $this->assertContains('Ant build file written', $display);
        $this->assertContains('Ant pre commit build file written', $display);
    }

    /**
     * @test
     */
    public function installWithTravisEnabled()
    {
        /** @var \Ibuildings\QA\tests\mock\InstallCommand $command */
        $command = $this->application->find('install');

        $dialog = $this->application->getDialogHelper();

        $startAt = 0;
        $this->addBaseExpects($dialog, $startAt);
        $this->addBuildArtifactExpects($dialog, $startAt);
        $this->addTravisEnabledExpects($dialog, $startAt);
        $this->addQAExpects($dialog, $startAt);
        $this->addPHPMDExpects($dialog, $startAt);
        $this->addPHPCSExpects($dialog, $startAt);
        $this->addCodeDuplicateExpects($dialog, $startAt);
        $this->addPhpUnitExpects($dialog, $startAt);
        $this->addFinishingExpects($dialog, $startAt);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()), array('verbosity' => OutputInterface::VERBOSITY_VERY_VERBOSE));

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/build_travis_enabled.xml',
            $command->buildXmlOutput
        );

        $this->assertXmlStringEqualsXmlFile(
            __DIR__ . '/fixtures/build-pre-commit.xml',
            $command->buildPreCommitXmlOutput
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/fixtures/.travis.yml',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\Common\Configurator\TravisConfigurator')->travisFileContents
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/fixtures/.travis.php.ini',
            $command->getConfiguratorRegistry()->getConfiguratorByName(
                'Ibuildings\QA\Tools\Common\Configurator\TravisConfigurator')->travisPhpIniContents
        );
    }


    /**
     * @test
     */
    public function installWithCustomPhpUnit()
    {
        /** @var InstallCommand $command */
        $command = $this->application->find('install');

        $dialog = $this->application->getDialogHelper();

        $startAt = 0;
        $this->addBaseExpects($dialog, $startAt);
        $this->addBuildArtifactExpects($dialog, $startAt);
        $this->addTravisExpects($dialog, $startAt);
        $this->addQAExpects($dialog, $startAt);
        $this->addPHPMDExpects($dialog, $startAt);
        $this->addPHPCSExpects($dialog, $startAt);
        $this->addCodeDuplicateExpects($dialog, $startAt);
        $this->addPhpUnitWithCustomPathExpects($dialog, $startAt);
        $this->addFinishingExpects($dialog, $startAt);

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
    protected function addBaseExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo(
                    "\nIf you already have a build config, it will be overwritten. Do you want to continue?"
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
    }

    /**
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addQAExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\nDo you want to install the QA tools for PHP?")
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
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable PHP Lint?')
            )
            ->will($this->returnValue(true));
    }

    /**
     * @param DialogHelper $dialog
     * @param int          $startAt
     */
    protected function addBuildArtifactExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("By default results are shown on the CLI (recommended if you are using travis)
  - Do you want to generate build artifacts?")
            )
            ->will($this->returnValue(false));
    }

    /**
     * @param DialogHelper $dialog
     * @param int          $startAt
     */
    protected function addTravisExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable Travis integration for this project?')
            )
            ->will($this->returnValue(false));
    }

    /**
     * @param DialogHelper $dialog
     * @param int          $startAt
     */
    protected function addTravisEnabledExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable Travis integration for this project?')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('select')
            ->with(
                $this->anything(),
                $this->equalTo("Which versions of php do you want to test this project on (enter the keys comma separated) [5.5]? ")
            )
            ->will($this->returnValue(array(1, 2)));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('You have chosen the following versions: "5.6", "5.5", is this correct? ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you need to set any environment variables for the CI server (e.g. SYMFONY_ENV or APPLICATION_ENV)? ')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo('Please enter the required variables, comma separated (e.g. FOO=bar,QUUZ=quux)')
            )
            ->will($this->returnValue(array('FOO=bar', 'QUUZ=quux')));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to enable Slack Notifications from Travis for this project?")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("Please paste your slack credentials \n"
                    . "  (see http://docs.travis-ci.com/user/notifications/#Slack-notifications): \n")
            )
            ->will($this->returnValue('ibuildings:somehashvalue'));
    }

    /**
     * Adds answers to the PHPMD questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPHPMDExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable the PHP Mess Detector?')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('  - Do you want to exclude custom patterns for PHP Mess Detector?')
            )
            ->will($this->returnValue(false));
    }

    /**
     * Adds answers to the PHPCS questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPHPCSExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable the PHP Code Sniffer?')
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
                $this->equalTo('  - Do you want to exclude some default Symfony patterns for PHP Code Sniffer?')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('  - Do you want to exclude some custom patterns for PHP Code Sniffer?')
            )
            ->will($this->returnValue(false));
    }

    /**
     * Add answers to the duplicate code questions
     *
     * @param DialogHelper $dialog
     * @param int          $startAt
     */
    protected function addCodeDuplicateExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to enable PHP Copy Paste Detection?")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to exclude patterns for PHP Copy Paste detection?")
            )
            ->will($this->returnValue(false));

        // @todo move to securityConfiguratorExpects()
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("Do you want to enable the Sensiolabs Security Checker?")
            )
            ->will($this->returnValue(true));

        // @todo move to sourcePathSinglePathExpects(), add sourcePathMultiPathExpects()
        // @todo after security, before phpunit
        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("At which paths is the PHP source code located? [src] (comma separated)\n")
            )
            ->will($this->returnValue(array('/tmp')));
    }

    /**
     * Adds answers to the phpunit questions.
     *
     * @param DialogHelper $dialog
     * @param int          $startAt at what position do we expect the first question
     */
    protected function addPhpUnitExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable PHPUnit tests?')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you have a custom PHPUnit config? (for example, Symfony has one in \'app/phpunit.xml.dist\')')
            )
            ->will($this->returnValue(false));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askAndValidate')
            ->with(
                $this->anything(),
                $this->equalTo("On what paths can the PHPUnit tests be found? [tests] (comma separated)\n")
            )
            ->will($this->returnValue(array('/tmp')));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable an autoload script for PHPUnit?')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
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
    protected function addPhpUnitWithCustomPathExpects(DialogHelper $dialog, &$startAt)
    {
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable PHPUnit tests?')
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you have a custom PHPUnit config? (for example, Symfony has one in \'app/phpunit.xml.dist\')')
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
    protected function addFinishingExpects(DialogHelper $dialog, &$startAt)
    {
        //Do you want to install the QA tools for Javascript? [Y/n]
        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\nDo you want to install the QA tools for Javascript?")
            )
            ->will($this->returnValue(true));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo('Do you want to enable JSHint?')
            )
            ->will($this->returnValue(false));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo("\nDo you want to install the Behat framework?")
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
                $this->equalTo("What is base url of the dev environment? [http://dev.test] ")
            )
            ->will($this->returnValue('http://dev.test'));

        $dialog
            ->expects($this->at($startAt++))
            ->method('askConfirmation')
            ->with(
                $this->anything(),
                $this->equalTo(
                    "\nDo you want to enable the git pre-commit hook? It will run the QA tools on every commit"
                )
            )
            ->will($this->returnValue(true));
    }
}
