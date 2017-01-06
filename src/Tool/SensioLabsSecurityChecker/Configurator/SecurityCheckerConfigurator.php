<?php

namespace Ibuildings\QaTools\Tool\SensioLabsSecurityChecker\Configurator;

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
use Ibuildings\QaTools\Tool\SensioLabsSecurityChecker\SensioLabsSecurityChecker;

final class SecurityCheckerConfigurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        $useSecurityChecker = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like to check for vulnerable dependencies using SensioLabs Security Checker?',
                YesOrNoAnswer::YES
            )
        );

        /** @var YesOrNoAnswer $useSecurityChecker */
        if ($useSecurityChecker->is(false)) {
            return;
        }

        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('sensiolabs/security-checker', '^3.0'));

        $antSnippet = $taskHelperSet->renderTemplate(
            'ant-build.xml.twig',
            ['targetName' => SensioLabsSecurityChecker::ANT_TARGET]
        );

        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Build::main(),
                Tool::withIdentifier(SensioLabsSecurityChecker::ANT_TARGET),
                Snippet::withContentsAndTargetName($antSnippet, SensioLabsSecurityChecker::ANT_TARGET)
            )
        );
    }

    public function getToolClassName()
    {
        return SensioLabsSecurityChecker::class;
    }
}
