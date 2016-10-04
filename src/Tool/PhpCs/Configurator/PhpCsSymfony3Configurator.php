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

final class PhpCsSymfony3Configurator implements Configurator
{
    /**
     * @see https://packagist.org/packages/endouble/symfony3-custom-coding-standard for more info on the coding standard
     */
    public function configure(
        Interviewer $interviewer,
        TaskDirectory $taskDirectory,
        TaskHelperSet $taskHelperSet
    ) {
        /** @var YesOrNoAnswer $usePhpCs */
        $usePhpCs = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::YES)
        );

        if ($usePhpCs->is(false)) {
            return; // do nothing
        }

        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('squizlabs/php_codesniffer', '^2.7'));
        $taskDirectory->registerTask(
            new InstallComposerDevDependencyTask('endouble/symfony3-custom-coding-standard', '~2.0')
        );

        $phpCsConfiguration = $taskHelperSet->renderTemplate(
            'ruleset-reference.xml.twig',
            ['ruleset' => 'vendor/escapestudios/symfony2-coding-standard/Symfony3Custom']
        );

        $taskDirectory->registerTask(
            new WriteFileTask(
                $taskDirectory->getProject()->getConfigurationFilesLocation()->getDirectory() . 'ruleset.xml',
                $phpCsConfiguration
            )
        );
    }

    public function getToolClassName()
    {
        return PhpCs::class;
    }
}
