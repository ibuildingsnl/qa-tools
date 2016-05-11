<?php

namespace Ibuildings\QaTools\Value\Answer;

use ArrayIterator;
use Countable;
use Ibuildings\QaTools\Assert\Assertion;
use IteratorAggregate;

final class MultipleAnswers implements Answer, IteratorAggregate, Countable
{
    /**
     * @var Answer[]
     */
    private $answers;

    /**
     * @param Answer[] $answers
     */
    public function __construct(array $answers)
    {
        Assertion::allIsInstanceOf($answers, Answer::class);

        $this->answers = $answers;
    }

    public function contains(Answer $other)
    {
        foreach ($this->answers as $answer) {
            if ($answer->equals($other)) {
                return true;
            }
        }

        return false;
    }

    public function equals(Answer $other)
    {
        if (!$other instanceof $this) {
            return false;
        }

        if (count($other) !== count($this)) {
            return false;
        }

        foreach ($other as $otherAnswer) {
            if (!$this->contains($otherAnswer)) {
                return false;
            }
        }

        return true;
    }

    public function getAnswer()
    {
        return $this->answers;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->answers);
    }

    public function count()
    {
        return count($this->answers);
    }
}
