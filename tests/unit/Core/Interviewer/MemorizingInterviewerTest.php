<?php

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Interviewer\MemorizingInterviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Interviewer
 * @group Configuration
 */
class MemorizingInterviewerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::all()
     */
    public function memorizing_interviewer_throws_exception_when_previous_answers_are_not_answers($previousAnswers)
    {
        $this->expectException(InvalidArgumentException::class);

        $dummyInterviewer = Mockery::mock(Interviewer::class);

        $memorizingInterviewer = new MemorizingInterviewer($dummyInterviewer, [$previousAnswers]);
    }

    /**
     * @test
     */
    public function memorizing_interviewer_throws_exception_when_previous_answers_have_no_hash_key()
    {
        $this->expectException(InvalidArgumentException::class);

        $wrongPreviousAnswers = [
            0 => new TextualAnswer('Answer'),
            1 => new TextualAnswer('Another answer'),
        ];

        $dummyInterviewer = Mockery::mock(Interviewer::class);

        $memorizingInterviewer = new MemorizingInterviewer($dummyInterviewer, $wrongPreviousAnswers);
    }

    /**
     * @test
     */
    public function memorizing_interviewer_passes_questions_to_interviewer_if_no_previous_answer()
    {
        $question = new TextualQuestion('A question');

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with($question);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, []);
        $memorizingInterviewer->ask($question);
    }

    /**
     * @test
     */
    public function memorizing_interviewer_passes_questions_with_a_suggested_default_answer_to_interviewer_if_previous_answer_present()
    {
        $scope = 'A\Class\Scope';
        $question = new TextualQuestion('A question');
        $suggestion = new TextualAnswer('Suggestion');
        $sameQuestionWithSuggestedDefaultAnswer = new TextualQuestion('A question', $suggestion);

        $questionIdentifier = md5($question.$scope);

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with(Mockery::on(function (Question $question) use ($sameQuestionWithSuggestedDefaultAnswer) {
                return $question->equals($sameQuestionWithSuggestedDefaultAnswer);
            }));

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, [$questionIdentifier => $suggestion]);
        $memorizingInterviewer->setScope($scope);

        $memorizingInterviewer->ask($question);
    }

    /**
     * @test
     */
    public function memorizing_interviewer_produces_given_answers_after_an_interview()
    {
        $scope = 'A\Class\Scope';
        $question = new TextualQuestion('A question');
        $answer   = new TextualAnswer('The answer');

        $questionIdentifier = md5($question.$scope);

        $expectedGivenAnswers = [$questionIdentifier => $answer];

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('ask')
            ->with($question)
            ->andReturn($answer);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, []);
        $memorizingInterviewer->setScope($scope);

        $memorizingInterviewer->ask($question);

        $this->assertEquals($expectedGivenAnswers, $memorizingInterviewer->getGivenAnswers());
    }

    /**
     * @test
     */
    public function memorizing_interviewer_passes_saying_messages_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('say')
            ->with($message);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, []);
        $memorizingInterviewer->say($message);
    }

    /**
     * @test
     */
    public function memorizing_interviewer_passes_warnings_to_interviewer()
    {
        $message = 'test message';

        $mockInterviewer = Mockery::mock(Interviewer::class);
        $mockInterviewer
            ->shouldReceive('warn')
            ->with($message);

        $memorizingInterviewer = new MemorizingInterviewer($mockInterviewer, []);
        $memorizingInterviewer->warn($message);
    }
}
