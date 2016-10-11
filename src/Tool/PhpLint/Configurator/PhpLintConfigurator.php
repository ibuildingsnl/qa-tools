<?php

namespace Ibuildings\QaTools\Tool\PhpLint\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Stages\Build;
use Ibuildings\QaTools\Core\Stages\Precommit;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
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
            ['targetName' => PhpLint::TARGET_NAME_FULL]
        );

        $taskDirectory->registerTask(
            new AddBuildTask(new Build(), $antFullSnippet, PhpLint::TARGET_NAME_FULL)
        );

        $antPrecommitSnippet = $taskHelperSet->renderTemplate(
            'ant-diff.xml.twig',
            ['targetName' => PhpLint::TARGET_NAME_DIFF]
        );

        $taskDirectory->registerTask(
            new AddBuildTask(new Precommit(), $antPrecommitSnippet, PhpLint::TARGET_NAME_DIFF)
        );
    }

    public function getToolClassName()
    {
        return PhpLint::class;
    }
}
