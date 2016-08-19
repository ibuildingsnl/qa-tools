<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Core\Project\Project;
use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;

final class ProjectConfigurator
{
    /**
     * @param Interviewer   $interviewer
     * @param Configuration $configuration
     * @param Directory     $projectDirectory
     * @return Project
     */
    public function configure(Interviewer $interviewer, Configuration $configuration, Directory $projectDirectory)
    {
        $interviewer->say(
            sprintf(
                'Configuring the %s (%s).',
                Application::NAME,
                Application::VERSION
            )
        );

        $nameOfProjectAnswer = $interviewer->ask(QuestionFactory::create('What is the project\'s name?'));

        $configFileLocationAnswer = $interviewer->ask(
            QuestionFactory::create('Where would you like to store the generated files?', './')
        );

        $projectCategoryAnswer = $interviewer->ask(
            QuestionFactory::createMultipleChoice(
                'What type of project would you like to configure?',
                [
                    'PHP',
                    'JavaScript',
                    sprintf('%s and %s', 'PHP', 'JavaScript'),
                ]
            )
        );

        $projectTypeAnswers = [];

        if ($projectCategoryAnswer->equals(new TextualAnswer('PHP'))
            || $projectCategoryAnswer->equals(new TextualAnswer('PHP and JavaScript'))
        ) {
            $projectTypeAnswers[] = $interviewer->ask(
                QuestionFactory::createMultipleChoice(
                    'What type of PHP project would you like to configure?',
                    [
                        'Symfony 2',
                        'Symfony 3',
                        'Drupal 7',
                        'Drupal 8',
                        'Other PHP Project',
                    ]
                )
            );
        }

        if ($projectCategoryAnswer->equals(new TextualAnswer('JavaScript'))
            || $projectCategoryAnswer->equals(new TextualAnswer('PHP and JavaScript'))
        ) {
            $projectTypeAnswers[] = $interviewer->ask(
                QuestionFactory::createMultipleChoice(
                    'What type of JavaScript project would you like to configure?',
                    [
                        'AngularJS 1',
                        'Angular 2',
                        'Other JS Project',
                    ]
                )
            );
        }

        $projectTypes = new ProjectTypeSet(
            array_map(
                function (TextualAnswer $answer) {
                    return ProjectType::fromHumanReadableString($answer->getRaw());
                },
                $projectTypeAnswers
            )
        );

        $travisEnabledAnswer = $interviewer->ask(
            QuestionFactory::createYesOrNo(
                'Would you like to integrate Travis in your project?',
                YesOrNoAnswer::YES
            )
        );

        $configuration->reconfigureProject(
            new Project(
                $nameOfProjectAnswer->getRaw(),
                $projectDirectory,
                new Directory($configFileLocationAnswer->getRaw()),
                $projectTypes,
                $travisEnabledAnswer->getRaw()
            )
        );
    }
}
