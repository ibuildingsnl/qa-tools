<?php

namespace Ibuildings\QaTools\Tool\PhpMd\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Stages\Build;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

final class PhpMdConfigurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        /** @var YesOrNoAnswer $usePhpMd */
        $usePhpMd = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to use PHP Mess Detector?', YesOrNoAnswer::YES)
        );
        if ($usePhpMd->is(YesOrNoAnswer::NO)) {
            return; //do nothing
        }


        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('phpmd/phpmd', '^2.0'));

        $project = $taskDirectory->getProject();
        $configurationFilesLocation = $project->getConfigurationFilesLocation();

        $phpMdConfiguration = $taskHelperSet->renderTemplate('phpmd-default.xml.twig', ['project' => $project]);
        $taskDirectory->registerTask(
            new WriteFileTask($configurationFilesLocation->getDirectory() . 'phpmd.xml', $phpMdConfiguration)
        );

        $antBuildSnippet = $taskHelperSet->renderTemplate('ant-build.xml.twig', ['targetName' => 'phpmd']);
        $taskDirectory->registerTask(
            new AddBuildTask(new Build(), $antBuildSnippet, 'phpmd')
        );
    }

    public function getToolClassName()
    {
        return PhpMd::class;
    }
}
