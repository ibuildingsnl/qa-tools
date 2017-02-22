<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Humbug\SelfUpdate\Updater;
use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\PharUpdater\Strategy\GitHubReleasesApiStrategy;
use Phar;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class SelfUpdateCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates Ibuildings QA Tools to the latest version')
            ->setHelp('Updates Ibuildings QA Tools to the latest version')
            ->addOption('rollback', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var LoggerInterface $logger */
        $logger = $this->container->get('logger');

        $io = new SymfonyStyle($input, $output);

        $logger->notice('Verifying that this instance of Ibuildings QA Tools is a Phar release build');
        if (Application::VERSION === '@' . 'package_version' . '@') {
            $io->error('This instance of Ibuildings QA Tools is not a release build; it cannot be self-updated.');

            return 1;
        }
        if (Phar::running() === '') {
            $io->error(
                'While you appear to be running a release build, this instance of Ibuildings QA Tools is not a Phar.'
            );

            return 1;
        }

        $updaterStrategy = new GitHubReleasesApiStrategy(
            new GuzzleHttpClient(),
            'ibuildingsnl',
            'qa-tools',
            'qa-tools.phar',
            Application::VERSION
        );
        $updater = new Updater();
        $updater->setStrategyObject($updaterStrategy);

        if ($input->getOption('rollback')) {
            return $this->rollBack($io, $logger, $updater);
        } else {
            return $this->selfUpdate($io, $logger, $updater);
        }
    }

    /**
     * @param SymfonyStyle    $io
     * @param LoggerInterface $logger
     * @param Updater         $updater
     * @return int
     */
    private function selfUpdate(SymfonyStyle $io, LoggerInterface $logger, Updater $updater)
    {
        $logger->notice(
            sprintf('Attempting to update from version %s to the latest version', Application::VERSION)
        );
        $io->text(
            sprintf(
                '<info>Attempting to update from version %s to the latest version...</info>',
                Application::VERSION
            )
        );

        try {
            $updateSucceeded = $updater->update();
        } catch (Exception $e) {
            $logger->error(
                sprintf('Something went wrong while updating to the latest version: "%s"', $e->getMessage())
            );
            $io->error(sprintf('Something went wrong while updating to the latest version: "%s"', $e->getMessage()));

            return 1;
        }

        if ($updateSucceeded) {
            $logger->notice(
                sprintf('Ibuildings QA Tools has been updated to version %s', $updater->getNewVersion())
            );
            $io->text(
                sprintf(
                    '<info>Ibuildings QA Tools has been updated to version %s</info>',
                    $updater->getNewVersion()
                )
            );
        } else {
            $logger->notice('Ibuildings QA Tools is already up-to-date');
            $io->text('<info>Ibuildings QA Tools is already up-to-date.</info>');
        }

        return 0;
    }

    /**
     * @param SymfonyStyle    $io
     * @param LoggerInterface $logger
     * @param Updater         $updater
     * @return int
     */
    private function rollBack(SymfonyStyle $io, LoggerInterface $logger, Updater $updater)
    {
        $logger->notice('Attempting to roll back to previous version');
        $io->text('<info>Attempting to roll back to previous version...</info>');

        try {
            $rollBackSucceeded = $updater->rollback();
        } catch (Exception $e) {
            $logger->error(
                sprintf('Something went wrong while updating to the latest version: "%s"', $e->getMessage())
            );
            $io->error(sprintf('Something went wrong while updating to the latest version: "%s"', $e->getMessage()));

            return 1;
        }

        if ($rollBackSucceeded) {
            $logger->notice('Ibuildings QA Tools has been rolled back to the previously installed version');
            $io->text(
                '<info>Ibuildings QA Tools has been rolled back to the previously installed version</info>'
            );
        } else {
            $logger->notice('The restoration of the old Phar failed due to an unknown reason');
            $io->text('<info>The restoration of the old Phar failed due to an unknown reason.</info>');
        }

        return 0;
    }
}
