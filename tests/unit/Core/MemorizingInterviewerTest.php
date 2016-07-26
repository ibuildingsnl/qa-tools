<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\MemorizingInterviewer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Interviewer
 * @group Configuration
 */
class MemorizingInterviewerTest extends TestCase
{
    /**
     * @test
     */
    public function passes_questions_to_interviewer_if_no_previous_answer()
    {
        $question = new TextualQuestion('A question');
        $answer = new TextualAnswer('An answer');

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with($question)
            ->andReturn($answer);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);
        $configuration->shouldReceive('hasAnswer')->with(Mockery::type('string'))->andReturn(false);
        $configuration->shouldReceive('answer');

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->ask($question);
    }

    /**
     * @test
     */
    public function passes_questions_with_a_suggested_answer_to_interviewer_if_configuration_has_a_previous_answer_stored()
    {
        $scope = 'A\Class\Scope';
        $question = new TextualQuestion('A question');
        $suggestion = new TextualAnswer('Suggestion');
        $newAnswer = new TextualAnswer('New answer');
        $sameQuestionWithSuggestedDefaultAnswer = new TextualQuestion('A question', $suggestion);

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with(Mockery::on(function (Question $question) use ($sameQuestionWithSuggestedDefaultAnswer) {
                return $question->equals($sameQuestionWithSuggestedDefaultAnswer);
            }))
            ->andReturn($newAnswer);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);
        $configuration->shouldReceive('hasAnswer')->with(Mockery::type('string'))->andReturn(true);
        $configuration->shouldReceive('getAnswer')->with(Mockery::type('string'))->andReturn($suggestion);
        $configuration->shouldReceive('answer');

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->setScope($scope);

        $memorizingInterviewer->ask($question);
    }

    /**
     * @test
     */
    public function stores_given_answers_in_configuration_during_the_interview()
    {
        $scope = 'A\Class\Scope';
        $question = new TextualQuestion('A question');
        $answer   = new TextualAnswer('The answer');

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with($question)
            ->andReturn($answer);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);
        $configuration->shouldReceive('hasAnswer')->with(Mockery::type('string'))->andReturn(false);
        $configuration->shouldReceive('answer')->with(Mockery::type('string'), $answer)->once();

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->setScope($scope);

        $memorizingInterviewer->ask($question);
    }

    /**
     * @test
     */
    public function passes_saying_messages_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('say')
            ->with($message);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->say($message);
    }

    /**
     * @test
     */
    public function passes_warnings_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('warn')
            ->with($message);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->warn($message);
    }
}
