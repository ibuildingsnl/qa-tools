<?php

namespace Ibuildings\QaTools\Tool\PhpMd\Configurator;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Task\ComposerDevDependencyTask;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

final class PhpMdSf2Configurator implements Configurator
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
        if ($usePhpMd->is(true)) {
            $packagePhpMd2 = Package::of('phpmd/phpmd', '^2.0');
            $taskDirectory->registerTask(new ComposerDevDependencyTask($packagePhpMd2));
        }
    }

    public function getToolClassName()
    {
        return PhpMd::class;
    }
}
