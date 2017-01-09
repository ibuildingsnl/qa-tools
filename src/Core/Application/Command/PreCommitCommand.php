<?php

namespace Ibuildings\QaTools\Core\Application\Command;

use Ibuildings\QaTools\Core\GitHook\GitHookInstaller;
use Ibuildings\QaTools\Core\Project\Directory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class PreCommitCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected function configure()
    {
        $this
            ->setName('configure:pre-commit')
            ->setDescription('Configure the pre-commit hook for the Ibuildings QA Tools')
            ->setHelp('Configure the pre-commit hook for the Ibuildings QA Tools');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $installer = $this->container->get('qa_tools.git.hook_installer');

        /** @var SymfonyQuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $projectRoot = new Directory(getcwd());

        if ($installer->preCommitHookExist($projectRoot)) {
            $question = new ConfirmationQuestion(
                '<question>A pre-commit hook already exists in this project. Are you sure you want to overwrite it? (y/N)</question>',
                false
            );

            if (!$questionHelper->ask($input, $output, $question)) {
                $output->writeln(
                    '<info>The pre-commit hook was left unchanged. You can manually add `ant precommit` to your pre-commit hook in order to run the pre-commit build before every commit.</info>'
                );

                return;
            }
        }

        $installer->installPreCommitHook($projectRoot);

        $output->writeln('<info>Installed Git pre-commit hook in '.GitHookInstaller::PRE_COMMIT_PATH.'</info>');
    }
}
