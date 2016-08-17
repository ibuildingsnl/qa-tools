<?php

namespace Ibuildings\QaTools\Tool\PhpMd\Configurator;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Configuration\RequirementDirectory;
use Ibuildings\QaTools\Core\Configuration\RequirementHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Requirement\ComposerPackagesRequirement;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

final class PhpMdSf2Configurator implements Configurator
{
    public function configure(
        Interviewer $interviewer,
        RequirementDirectory $requirementDirectory,
        RequirementHelperSet $requirementHelperSet
    ) {
        $usePhpMd = $interviewer->ask(
            QuestionFactory::createYesOrNo('Would you like to use PHP Mess Detector?', YesOrNoAnswer::YES)
        );
        if ($usePhpMd) {
            $packagePhpMd2 = Package::of('phpmd/phpmd', '^2.0');
            $requirementDirectory->registerRequirement(
                new ComposerPackagesRequirement($packagePhpMd2),
                $this->getToolClassName()
            );
        }
    }

    /**
     * @return string
     */
    public function getToolClassName()
    {
        return PhpMd::class;
    }
}
