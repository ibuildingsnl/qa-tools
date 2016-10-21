<?php

namespace Ibuildings\QaTools\UnitTest\Core\Configuration;

use Ibuildings\QaTools\Core\Configuration\Configuration;
use Ibuildings\QaTools\Core\Configuration\MemorizingInterviewer;
use Ibuildings\QaTools\Core\Configuration\QuestionId;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Mockery;
use Mockery\Matcher\MatcherAbstract;
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
        $scope = 'A\Class\Scope';
        $question = new TextualQuestion('A question');
        $answer = new TextualAnswer('An answer');

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with($question)
            ->andReturn($answer);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);
        $configuration
            ->shouldReceive('hasAnswer')
            ->with(self::voEquals(QuestionId::fromScopeAndQuestion($scope, $question)))
            ->andReturn(false);
        $configuration->shouldReceive('answer');

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->setScope($scope);
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
            ->with(self::voEquals($sameQuestionWithSuggestedDefaultAnswer))
            ->andReturn($newAnswer);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);
        $configuration
            ->shouldReceive('hasAnswer')
            ->with(self::voEquals(QuestionId::fromScopeAndQuestion($scope, $question)))
            ->andReturn(true);
        $configuration
            ->shouldReceive('getAnswer')
            ->with(self::voEquals(QuestionId::fromScopeAndQuestion($scope, $question)))
            ->andReturn($suggestion);
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
        $configuration
            ->shouldReceive('hasAnswer')
            ->with(self::voEquals(QuestionId::fromScopeAndQuestion($scope, $question)))
            ->andReturn(false);
        $configuration
            ->shouldReceive('answer')
            ->with(self::voEquals(QuestionId::fromScopeAndQuestion($scope, $question)), $answer)
            ->once();

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->setScope($scope);

        $memorizingInterviewer->ask($question);
    }

    /**
     * @test
     */
    public function passes_notices_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('notice')
            ->with($message);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->notice($message);
    }

    /**
     * @test
     */
    public function passes_details_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('giveDetails')
            ->with($message);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->giveDetails($message);
    }

    /**
     * @test
     */
    public function passes_success_messages_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('success')
            ->with($message);

        /** @var MockInterface|Configuration $configuration */
        $configuration = Mockery::mock(Configuration::class);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, $configuration);
        $memorizingInterviewer->success($message);
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

    /**
     * @param object $expected
     * @return MatcherAbstract
     */
    public static function voEquals($expected)
    {
        return Mockery::on(function ($actual) use ($expected) {
            return $actual->equals($expected);
        });
    }
}
