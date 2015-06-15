<?php
/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\PHP\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

/**
 * Class MiniumCodeCoverageCommand
 * @package Ibuildings\QA\Tools\PHP\Console
 */
class CodeCoverageCheckCommand extends Command
{
    /**
     * @var string
     */
    private $cloverReportFile;

    /**
     * @param string $cloverReportFile
     */
    public function setCloverReportFile($cloverReportFile)
    {
        $this->cloverReportFile = $cloverReportFile;
    }

    protected function configure()
    {
        $this
            ->setName('minimum-code-coverage')
            ->setDescription('Checks if the amount of code that is covered by unit tests')
            ->setHelp('Runs the Ibuildings QA Tools on the current changeset')
            ->addArgument(
                'minimum',
                InputArgument::REQUIRED,
                'Minimum coverage'
            );
    }

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $minimumCoverage = $input->getArgument('minimum');
        $coverage = $this->loadLineCoverage();

        if ($coverage < $minimumCoverage) {
            $output->writeln("Coverage of {$coverage}% is lower than minimum coverage of {$minimumCoverage}%");
            return 1;
        }
    }

    /**
     * Loads coverage from Clover report file
     *
     * @return integer
     */
    protected function loadLineCoverage()
    {
        $cloverReport = simplexml_load_file($this->cloverReportFile);
        $metrics = $cloverReport->project->metrics;
        $coverage = ($metrics['coveredstatements'] / $metrics['statements']) * 100;
        return round($coverage);
    }
}
