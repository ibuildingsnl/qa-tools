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

/**
 * Class MiniumCodeCoverageCommand
 * @package Ibuildings\QA\Tools\PHP\Console
 */
class CodeCoverageCheckCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('minimum-code-coverage')
            ->setDescription('Checks if the amount of code that is covered by unit tests')
            ->setHelp('Runs the Ibuildings QA Tools on the current changeset')
            ->addArgument(
                'clover-report-file',
                InputArgument::REQUIRED,
                'Path to clover report file'
            )
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
        $cloverReportFile = $input->getArgument('clover-report-file');
        $minimumCoverage = $input->getArgument('minimum');
        $coverage = $this->loadLineCoverage($cloverReportFile);

        if ($coverage < $minimumCoverage) {
            $output->writeln("Coverage of {$coverage}% is lower than minimum coverage of {$minimumCoverage}%");
            return 1;
        }
    }

    /**
     * Loads coverage from Clover report file
     * @param string $cloverReportFile
     * @return integer
     */
    protected function loadLineCoverage($cloverReportFile)
    {
        $cloverReport = simplexml_load_file($cloverReportFile);
        $metrics = $cloverReport->project->metrics;
        $coverage = ($metrics['coveredstatements'] / $metrics['statements']) * 100;
        return round($coverage);
    }
}
