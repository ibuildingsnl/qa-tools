<?php

namespace Ibuildings\QaTools\Tool\PhpUnit\Configurator;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Task\AddAntBuildTask;
use Ibuildings\QaTools\Core\Task\InstallComposerDevDependencyTask;
use Ibuildings\QaTools\Core\Task\WriteFileTask;
use Ibuildings\QaTools\Tool\PhpUnit\PhpUnit;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class PhpUnitConfigurator implements Configurator
{
    /**
     * @var string
     */
    private $phpunitConfigurationTemplate;

    public function __construct($phpunitConfigurationTemplate)
    {
        Assertion::string($phpunitConfigurationTemplate);
        Assertion::file(__DIR__ . '/../Resources/templates/' . $phpunitConfigurationTemplate);

        $this->phpunitConfigurationTemplate = $phpunitConfigurationTemplate;
    }

    public function configure(Interviewer $interviewer, TaskDirectory $taskDirectory, TaskHelperSet $taskHelperSet)
    {
        $usePhpUnit = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like to install PHPUnit for running automated tests?',
                YesOrNoAnswer::YES
            )
        );

        /** @var YesOrNoAnswer $usePhpUnit */
        if ($usePhpUnit->is(YesOrNoAnswer::NO)) {
            return;
        }

        $taskDirectory->registerTask(new InstallComposerDevDependencyTask('phpunit/phpunit', '^5.7'));

        $taskDirectory->registerTask(
            new WriteFileTask(
                $taskDirectory->getProject()->getConfigurationFilesLocation()->getDirectory() . 'phpunit.xml',
                $taskHelperSet->renderTemplate($this->phpunitConfigurationTemplate)
            )
        );

        $antSnippet = $taskHelperSet->renderTemplate(
            'ant-build.xml.twig',
            ['targetName' => PhpUnit::ANT_TARGET]
        );

        $taskDirectory->registerTask(
            new AddAntBuildTask(
                Build::main(),
                Tool::withIdentifier(PhpUnit::ANT_TARGET),
                Snippet::withContentsAndTargetName($antSnippet, PhpUnit::ANT_TARGET)
            )
        );
    }

    /**
     * @return string
     */
    public function getToolClassName()
    {
        return PhpUnit::class;
    }
}
