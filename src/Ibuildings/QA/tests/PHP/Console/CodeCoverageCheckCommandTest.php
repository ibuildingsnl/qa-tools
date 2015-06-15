<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tests\PHP\Console;

use Ibuildings\QA\Tools\Common\Application;
use Ibuildings\QA\Tools\Common\Settings;
use Ibuildings\QA\Tools\PHP\Console\CodeCoverageCheckCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RunCommandTest
 *
 * @package Ibuildings\QA\tests\PHP\Console
 */
class CodeCoverageCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    private $cloverReportFile;

    public function setup()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../../');
        $packageBaseDir = realpath(__DIR__ . '/../../../../../../');
        $settings = new Settings($baseDir, $packageBaseDir);

        $this->application = new Application('ibuildings qa tools', $settings);

        $codeCoverageCheckCommand = new CodeCoverageCheckCommand();
        $this->cloverReportFile = '/tmp/test-clover-report.xml';
        $codeCoverageCheckCommand->setCloverReportFile($this->cloverReportFile);
        $this->application->add($codeCoverageCheckCommand);
    }

    /**
     * @test
     */
    public function shouldExitWhenCoverageIsTooLow()
    {

        /** @var codeCoverageCheckCommand $command */
        $command = $this->application->find('minimum-code-coverage');

        $commandTester = new CommandTester($command);

        $cloverReport = <<<XML
<coverage>
    <project>
        <metrics
        statements="100"
        coveredstatements="10"
        />
  </project>
</coverage>
XML;
        file_put_contents($this->cloverReportFile, $cloverReport);

        $exitCode = $commandTester->execute(
            array(
                'command' => $command->getName(),
                'minimum' => 100
            )
        );

//        $this->setExpectedException('RuntimeExceptio, ')

        $this->assertEquals(1, $exitCode);


        $display = $commandTester->getDisplay();
        $this->assertContains('Coverage of 10% is lower than minimum coverage of 100%', $display);
    }
}
