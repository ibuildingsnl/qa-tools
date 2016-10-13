<?php

namespace Ibuildings\QaTools\Tool\PhpCs\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Target;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Task\AddBuildTask;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Tool\PhpCs\PhpCs;

final class PhpCsSymfony2Configurator implements Configurator
{
    /**
     * @see https://github.com/djoos/Symfony2-coding-standard for more info on the coding standard
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
            new InstallComposerDevDependencyTask('escapestudios/symfony2-coding-standard', '~2.0')
        );

        $phpCsConfiguration = $taskHelperSet->renderTemplate(
            'ruleset-reference.xml.twig',
            ['ruleset' => 'vendor/escapestudios/symfony2-coding-standard/Symfony2']
        );

        $taskDirectory->registerTask(
            new WriteFileTask(
                $taskDirectory->getProject()->getConfigurationFilesLocation()->getDirectory() . 'ruleset.xml',
                $phpCsConfiguration
            )
        );

        $antSnippet = $taskHelperSet->renderTemplate(
            'ant-build.xml.twig',
            ['targetName' => PhpCs::ANT_TARGET]
        );

        $taskDirectory->registerTask(
            new AddBuildTask(
                Target::build(),
                Tool::withIdentifier('phpcs'),
                Snippet::withContentsAndTargetName($antSnippet, PhpCs::ANT_TARGET)
            )
        );
    }

    public function getToolClassName()
    {
        return PhpCs::class;
    }
}
