<?php

namespace Ibuildings\QaTools\Tool\PhpLint\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Target;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
use Ibuildings\QaTools\Tool\PhpLint\PhpLint;

final class PhpLintConfigurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        /** @var YesOrNoAnswer $usePhpLint */
        $usePhpLint = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to use PHP Lint?', YesOrNoAnswer::YES)
        );

        if ($usePhpLint->is(YesOrNoAnswer::NO)) {
            return; //do nothing
        }

        $antFullSnippet = $taskHelperSet->renderTemplate(
            'ant-full.xml.twig',
            ['targetName' => PhpLint::ANT_TARGET_FULL]
        );

        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Target::build(),
                Tool::withIdentifier('phplint'),
                Snippet::withContentsAndTargetName($antFullSnippet, PhpLint::ANT_TARGET_FULL)
            )
        );

        $antPrecommitSnippet = $taskHelperSet->renderTemplate(
            'ant-diff.xml.twig',
            ['targetName' => PhpLint::ANT_TARGET_DIFF]
        );

        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Target::preCommit(),
                Tool::withIdentifier('phplint'),
                Snippet::withContentsAndTargetName($antPrecommitSnippet, PhpLint::ANT_TARGET_DIFF)
            )
        );
    }

    public function getToolClassName()
    {
        return PhpLint::class;
    }
}
