<?php

namespace Ibuildings\QaTools\Tool\PhpCs\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Project\ProjectType;
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
        $projectTypeSet = $taskDirectory->getProject()->getProjectTypes();
        $isDrupal = $projectTypeSet->contains(new ProjectType(ProjectType::TYPE_PHP_DRUPAL_7)) ||
                    $projectTypeSet->contains(new ProjectType(ProjectType::TYPE_PHP_DRUPAL_8));

        /** @var YesOrNoAnswer $usePhpCs */
        $usePhpCs = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to use PHP Code Sniffer?', YesOrNoAnswer::YES)
        );

        if ($usePhpCs->is(false)) {
            return; // do nothing
        }

        /** @var TextualAnswer $baseRuleset */
        $baseRuleset = $interviewer->ask(
            QuestionFactory::createMultipleChoice(
                'What ruleset would you like to use a base?',
                ['PSR1', 'PSR2', 'Squiz', 'Zend'],
                'PSR2'
            )
        );

        /** @var YesOrNoAnswer $beLessStrictAboutLineLength */
        $beLessStrictAboutLineLength = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like to allow longer lines than the default? Warn at 120 and fail at 150.',
                YesOrNoAnswer::YES
            )
        );

        /** @var YesOrNoAnswer $beLessStrictAboutDocblocksInTests */
        $beLessStrictAboutDocblocksInTests = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like be less strict about doc blocks in tests?',
                YesOrNoAnswer::YES
            )
        );

        $testLocation = 'tests/*';
        if ($beLessStrictAboutDocblocksInTests->is(true)) {
            $testLocation = $interviewer
                ->ask(QuestionFactory::create('Where are your tests located?', $testLocation))
                ->getRaw();
        }

        /** @var YesOrNoAnswer $beLessStrictAboutDocblocksInTests */
        $shouldIgnoreSomeLocationsCompletely = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like PHPCS to ignore some locations completely?',
                YesOrNoAnswer::YES
            )
        );

        $ignoredLocation = 'behat/*';
        if ($shouldIgnoreSomeLocationsCompletely->is(true)) {
            $ignoredLocation = $interviewer
                ->ask(QuestionFactory::create('Which locations should be ignored?', $ignoredLocation))
                ->getRaw();
        }

        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('squizlabs/php_codesniffer', '^2.7'));

        if ($isDrupal) {
            $taskDirectory->registerTask(new InstallComposerDevDependencyTask('drupal/coder', '8.*'));
        }

        $phpMdConfiguration = $taskHelperSet->renderTemplate(
            $isDrupal ? 'ruleset-drupal8.xml.twig' : 'ruleset.xml.twig',
            [
                'baseRuleset' => $baseRuleset->getRaw(),
                'beLessStrictAboutLineLength' => $beLessStrictAboutLineLength->is(true),
                'beLessStrictAboutDocblocksInTests' => $beLessStrictAboutDocblocksInTests->is(true),
                'shouldIgnoreSomeLocationsCompletely' => $shouldIgnoreSomeLocationsCompletely,
                'testLocation' => $testLocation,
                'ignoredLocation' => $ignoredLocation,
            ]
        );

        $taskDirectory->registerTask(
            new WriteFileTask(
                $taskDirectory->getProject()->getConfigurationFilesLocation()->getDirectory() . 'ruleset.xml',
                $phpMdConfiguration
            )
        );
    }

    public function getToolClassName()
    {
        return PhpCs::class;
    }
}
