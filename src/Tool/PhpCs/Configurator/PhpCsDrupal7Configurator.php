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

final class PhpCsDrupal7Configurator implements Configurator
{
    /**
     * @see https://www.drupal.org/node/1419988 for more info on the coding standard
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
        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('drupal/coder', '7.*'));

        $phpCsConfiguration = $taskHelperSet->renderTemplate(
            'ruleset-reference.xml.twig',
            ['ruleset' => 'vendor/drupal/coder/coder_sniffer/Drupal']
        );

        $taskDirectory->registerTask(
            new WriteFileTask(
                $taskDirectory->getProject()->getConfigurationFilesLocation()->getDirectory() . 'ruleset.xml',
                $phpCsConfiguration
            )
        );

        $antSnippet = $taskHelperSet->renderTemplate(
            'ant-build.xml.twig',
            ['targetName' => PhpCs::TARGET_NAME]
        );

        $taskDirectory->registerTask(
            new AddBuildTask(
                Target::build(),
                Tool::withIdentifier('phpcs'),
                Snippet::withContentsAndTargetName($antSnippet, PhpCs::TARGET_NAME)
            )
        );
    }

    public function getToolClassName()
    {
        return PhpCs::class;
    }
}
