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
use Ibuildings\QA\Tools\Common\Console\ChangeSetPreCommitCommand;
use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ChangeSetPreCommitCommandTest
 *
 * @package Ibuildings\QA\tests\Common\Console
 */
class ChangeSetPreCommitCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $application;

    public function setup()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $changesetCommand = $this->getMock('Ibuildings\QA\Tools\Common\Console\ChangeSetPreCommitCommand', array('getChangeSet'));

        $this->application = new Application('ibuildings qa tools', '1.1.16', $settings);

        $changesetCommand->expects($this->any())
            ->method('getChangeSet')
            ->will($this->returnValue("test.php\ntest2.php\ntest2.js\ntest3.txt"));

        $this->application->add($changesetCommand);
    }

    /**
     * @test
     */
    public function testFilterOnExtensionPhp()
    {
        /** @var ChangeSetPreCommitCommand $command */
        $command = $this->application->find('changeset:pre-commit');

        $commandTester = new CommandTester($command);

        ob_start();
        $commandTester->execute(
            array(
                'command'      => $command->getName(),
                '--filter-ext' => array("php"),
                '--separator'  => ' ')
        );

        $output = trim(ob_get_contents());

        ob_end_clean();
        $this->assertContains('test.php', $output);
        $this->assertContains('test2.php', $output);
        $this->assertNotContains('test2.js', $output);
        $this->assertNotContains('test3.txt', $output);
    }

    /**
     * @test
     */
    public function testFilterOnExtensionJs()
    {
        /** @var ChangeSetPreCommitCommand $command */
        $command = $this->application->find('changeset:pre-commit');

        $commandTester = new CommandTester($command);

        ob_start();
        $commandTester->execute(
            array(
                'command'      => $command->getName(),
                '--filter-ext' => array("js"),
                '--separator'  => ' '
            )
        );

        $output = trim(ob_get_contents());

        ob_end_clean();
        $this->assertNotContains('test.php', $output);
        $this->assertNotContains('test2.php', $output);
        $this->assertContains('test2.js', $output);
        $this->assertNotContains('test3.txt', $output);
    }


    /**
     * @test
     */
    public function testFilterOnExtensionsPhpAndJs()
    {
        /** @var ChangeSetPreCommitCommand $command */
        $command = $this->application->find('changeset:pre-commit');

        $commandTester = new CommandTester($command);

        ob_start();
        $commandTester->execute(
            array(
                'command'      => $command->getName(),
                '--filter-ext' => array("php", "js"),
                '--separator'  => ' '
            )
        );

        $output = trim(ob_get_contents());

        ob_end_clean();
        $this->assertContains('test.php', $output);
        $this->assertContains('test2.php', $output);
        $this->assertContains('test2.js', $output);
        $this->assertNotContains('test3.txt', $output);
    }

    /**
     * @test
     */
    public function testNoFilterGiven()
    {
        /** @var ChangeSetPreCommitCommand $command */
        $command = $this->application->find('changeset:pre-commit');

        $commandTester = new CommandTester($command);

        ob_start();
        $commandTester->execute(
            array(
                'command'      => $command->getName(),
                '--filter-ext' => array(),
                '--separator'  => ' '
            )
        );

        $output = trim(ob_get_contents());

        ob_end_clean();
        $this->assertEquals('', $output);
    }

    /**
     * @test
     */
    public function estFilterOnExtensionPhpNoSeparatorGiven()
    {
        /** @var ChangeSetPreCommitCommand $command */
        $command = $this->application->find('changeset:pre-commit');

        $commandTester = new CommandTester($command);

        ob_start();

        $commandTester->execute(
            array(
                'command'      => $command->getName(),
                '--filter-ext' => array("php")
            )
        );

        $output = trim(ob_get_contents());

        ob_end_clean();
        $this->assertContains("test.php\ntest2.php", $output);
    }
}
