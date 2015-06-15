<?php
/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\PHP\CodeCoverage;

use Ibuildings\QA\Tools\Common\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MiniumCodeCoverageCommand
 * @package Ibuildings\QA\Tools\PHP\CodeCoverage
 */
class MinimumCodeCoverageCommand extends AbstractCommand
{

    protected function configure()
    {
        $this
            ->setName('minimum-code-coverage')
            ->setDescription('Checks if the amount of code that is covered by unit tests')
            ->setHelp('Runs the Ibuildings QA Tools on the current changeset')
            ->addArgument(
                'minimum-coverage',
                InputArgument::REQUIRED,
                'Minimum coverage'
            );
    }

    /**
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $minimumCoverage = $input->getArgument('minimum-coverage');
        $coverage = $this->loadLineCoverage();

        if ($coverage < $minimumCoverage) {
            echo "Coverage of {$coverage}% is lower than minimum coverage of {$minimumCoverage}%" . PHP_EOL;
            exit(1);
        }
    }

    /**
     * Loads coverage from Clover report file
     *
     * @return integer
     */
    protected function loadLineCoverage()
    {
        $cloverReport = simplexml_load_file('build/artifacts/logs/clover.xml');
        $metrics = $cloverReport->project->metrics;
        $coverage = ($metrics['coveredstatements'] / $metrics['statements']) * 100;
        return round($coverage);
    }
}
