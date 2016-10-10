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

        $phpLintAntSnippet = $taskHelperSet->renderTemplate(
            'snippet-phplint-build.xml.twig',
            ['targetName' => 'php-lint-full']
        );

        $phpLintPrecommitAntSnippet = $taskHelperSet->renderTemplate(
            'snippet-phplint-diff.xml.twig',
            ['targetName' => 'php-lint-diff']
        );

        $taskDirectory->registerTask(
            new AddBuildTask(new Build(), $phpLintAntSnippet, 'php-lint-full')
        );
        $taskDirectory->registerTask(
            new AddBuildTask(new Precommit(), $phpLintPrecommitAntSnippet, 'php-lint-diff')
        );
    }

    public function getToolClassName()
    {
        return PhpLint::class;
    }
}
