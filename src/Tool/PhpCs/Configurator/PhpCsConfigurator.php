<?php

namespace Ibuildings\QaTools\Tool\PhpCs\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Tool\PhpCs\PhpCs;

final class PhpCsConfigurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        /** @var YesOrNoAnswer $usePhpCs */
        $usePhpCs = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::YES)
        );
        if ($usePhpCs->is(true)) {
            $taskDirectory->registerTask(new InstallComposerDevDependencyTask('squizlabs/php_codesniffer', '^2.7'));

            $project = $taskDirectory->getProject();
            $configurationFilesLocation = $project->getConfigurationFilesLocation();
            $phpMdConfiguration = $taskHelperSet->renderTemplate('ruleset.xml.twig', ['project' => $project]);
            $taskDirectory->registerTask(
                new WriteFileTask($configurationFilesLocation->getDirectory() . 'ruleset.xml', $phpMdConfiguration)
            );
        }
    }

    public function getToolClassName()
    {
        return PhpCs::class;
    }
}
