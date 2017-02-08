<?php

namespace Ibuildings\QaTools\Tool\PhpParallelLint\Configurator;

use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Tool\PhpParallelLint\PhpParallelLint;

final class DrupalPhpParallelLintConfigurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        /** @var YesOrNoAnswer $usePhpParallelLint */
        $usePhpParallelLint = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to lint PHP files?', YesOrNoAnswer::YES)
        );

        if ($usePhpParallelLint->is(YesOrNoAnswer::NO)) {
            return; //do nothing
        }

        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('jakub-onderka/php-parallel-lint', '^0.9.2'));

        $antFullSnippet = $taskHelperSet->renderTemplate(
            'ant-full.xml.twig',
            [
                'targetName' => PhpParallelLint::ANT_TARGET_FULL,
                'extensions' => ['php', 'module', 'inc', 'theme', 'profile', 'install'],
            ]
        );


        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Build::main(),
                Tool::withIdentifier('phplint'),
                Snippet::withContentsAndTargetName($antFullSnippet, PhpParallelLint::ANT_TARGET_FULL)
            )
        );

        $antPrecommitSnippet = $taskHelperSet->renderTemplate(
            'ant-diff.xml.twig',
            [
                'targetName' => PhpParallelLint::ANT_TARGET_DIFF,
                'extensions' => ['php', 'module', 'inc', 'theme', 'profile', 'install'],
            ]
        );

        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Build::preCommit(),
                Tool::withIdentifier('phplint'),
                Snippet::withContentsAndTargetName($antPrecommitSnippet, PhpParallelLint::ANT_TARGET_DIFF)
            )
        );
    }

    public function getToolClassName()
    {
        return PhpParallelLint::class;
    }
}
