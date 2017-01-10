<?php

namespace Ibuildings\QaTools\Tool\Behat\Configurator;

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
use Ibuildings\QaTools\Tool\Behat\Behat;
use Ibuildings\QaTools\Tool\Behat\Task\InitialiseBehatTask;

final class BehatConfigurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        /** @var YesOrNoAnswer $installBehat */
        $installBehat = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to install Behat for feature testing?', YesOrNoAnswer::YES)
        );

        if ($installBehat->is(YesOrNoAnswer::NO)) {
            return;
        }

        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('behat/behat', '^3.0'));

        $taskDirectory->registerTask(new InitialiseBehatTask());

        $antSnippet = $taskHelperSet->renderTemplate(
            'ant-build.xml.twig',
            ['targetName' => Behat::ANT_TARGET]
        );

        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Build::main(),
                Tool::withIdentifier(Behat::ANT_TARGET),
                Snippet::withContentsAndTargetName($antSnippet, Behat::ANT_TARGET)
            )
        );
    }

    /**
     * @return string
     */
    public function getToolClassName()
    {
        return Behat::class;
    }
}
