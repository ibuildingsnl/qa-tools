<?php

namespace Ibuildings\QaTools\Core\Project;

use Ibuildings\QaTools\Core\Application\Application;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question;
use Ibuildings\QaTools\Core\IO\Cli\InterviewerFactory;

final class ProjectConfigurator
{
    /**
     * @param Interviewer $interviewer
     * @return Project
     */
    public function configure(Interviewer $interviewer)
    {
        $interviewer->say(
            sprintf(
                'Configuring the %s (%s).',
                Application::NAME,
                Application::VERSION
            )
        );

        $nameOfProjectAnswer = $interviewer->ask(Question::create('What is the project\'s name?'));

        $configFileLocationAnswer = $interviewer->ask(
            Question::create('Where would you like to store the generated files?', './')
        );

        $projectCategoryAnswer = $interviewer->ask(
            Question::createMultipleChoice(
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
                Question::createMultipleChoice(
                    'What type of PHP project would you like to configure?',
                    [
                        'Symfony 2',
                        'Symfony 3',
                        'Drupal 7',
                        'Drupal 8',
                        'Other PHP Project'
                    ]
                )
            );
        }

        if ($projectCategoryAnswer->equals(new TextualAnswer('JavaScript'))
            || $projectCategoryAnswer->equals(new TextualAnswer('PHP and JavaScript'))
        ) {
            $projectTypeAnswers[] = $interviewer->ask(
                Question::createMultipleChoice(
                    'What type of JavaScript project would you like to configure?',
                    [
                        'AngularJS 1',
                        'Angular 2',
                        'Other JS Project',
                    ]
                )
            );
        }

        $projectTypes = array_map(function (TextualAnswer $answer){
            return ProjectType::fromHumanReadableString($answer->getAnswer());
        }, $projectTypeAnswers);

        $travisEnabledAnswer = $interviewer->ask(
            Question::createYesOrNo(
                'Would you like to integrate Travis in your project?',
                YesOrNoAnswer::YES
            )
        );

        return new Project(
            $nameOfProjectAnswer->getAnswer(),
            $configFileLocationAnswer->getAnswer(),
            $projectTypes,
            $travisEnabledAnswer->getAnswer()
        );
    }
}
