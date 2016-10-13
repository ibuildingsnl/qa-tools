<?php

namespace Ibuildings\QaTools\Tool\PhpCs\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Target;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Tool\PhpCs\PhpCs;

final class PhpCsOtherConfigurator implements Configurator
{
    /**
     * This is a long script and readability will not improve by splitting this method up.
     * Therefore a suppressed warning.
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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

        /** @var TextualAnswer $baseRuleset */
        $baseRuleset = $interviewer->ask(
            QuestionFactory::createMultipleChoice(
                'What ruleset would you like to use as a base?',
                ['PSR1', 'PSR2', 'Squiz', 'Zend'],
                'PSR2'
            )
        );

        /** @var TextualAnswer $useCustomizedLineLengthSettings */
        $useCustomizedLineLengthSettings = $interviewer->ask(
            QuestionFactory::createMultipleChoice(
                'How would you like to handle line lengths?',
                [
                    'Warn when >120. Fail when >150',
                    'Use base ruleset setting: Zend will warn at 80 and fail at 120. PSR1 ignores line length. ' .
                        'PSR2 only warns at 80. Squiz only warns at 120',
                ],
                'Warn when >120. Fail when >150'
            )
        );

        /** @var YesOrNoAnswer $beLessStrictAboutDocblocksInTests */
        $beLessStrictAboutDocblocksInTests = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like to skip any sniffs regarding the doc blocks in tests?',
                YesOrNoAnswer::YES
            )
        );

        $testLocation = 'tests/*';
        if ($beLessStrictAboutDocblocksInTests->is(true)) {
            $testLocation = $interviewer
                ->ask(QuestionFactory::create(
                    'Where are the tests located for which doc block sniffs will be disabled?',
                    $testLocation
                ))
                ->getRaw();
        }

        /** @var YesOrNoAnswer $beLessStrictAboutDocblocksInTests */
        $shouldIgnoreSomeLocationsCompletely = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like PHPCS to ignore some locations completely? ' .
                '(you may use a regex to match multiple directories)',
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

        $phpCsConfiguration = $taskHelperSet->renderTemplate(
            'ruleset.xml.twig',
            [
                'baseRuleset' => $baseRuleset->getRaw(),
                'useCustomizedLineLengthSettings' =>
                    $useCustomizedLineLengthSettings->getRaw() === 'Warn when >120. Fail when >150',
                'beLessStrictAboutDocblocksInTests' => $beLessStrictAboutDocblocksInTests->is(true),
                'shouldIgnoreSomeLocationsCompletely' => $shouldIgnoreSomeLocationsCompletely,
                'testLocation' => $testLocation,
                'ignoredLocation' => $ignoredLocation,
            ]
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
            new AddAntBuildTask(
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
