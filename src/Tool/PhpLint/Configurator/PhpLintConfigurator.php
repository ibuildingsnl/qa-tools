<?php

namespace Ibuildings\QaTools\Tool\PhpLint\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
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

        /** @var string $directoriesToLint */
        $directoriesToLint = $interviewer->ask(
            QuestionFactory::create('What directories would you like to lint? Use comma\'s to separate', 'src,test')
        )->getRaw();

        $directories = array_filter(explode(',', $directoriesToLint));
        $atLeastOneDirectoryGiven = count($directories) > 0;

        if (!$atLeastOneDirectoryGiven) {
            $interviewer->say('You entered no directories, thefore PHP Lint will not be added to your project');
            return;
        }

        $project = $taskDirectory->getProject();
        $configurationFilesLocation = $project->getConfigurationFilesLocation();

        $phpLintExecutable = $taskHelperSet->renderTemplate('phplint.sh.twig', ['directories' => $directories]);

        $taskDirectory->registerTask(
            new WriteFileTask(
                $configurationFilesLocation->getDirectory() . 'phplint.sh',
                $phpLintExecutable,
                0755
            )
        );
    }

    public function getToolClassName()
    {
        return PhpLint::class;
    }
}
