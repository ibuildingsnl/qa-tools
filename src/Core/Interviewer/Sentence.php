<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class Sentence
{
    /**
     * @var string
     */
    private $sentence;

    public function __construct($sentence)
    {
        Assertion::string($sentence);
        $this->sentence = $sentence;
    }

    /**
     * @param Sentence $other
     * @return boolean
     */
    public function equals(Sentence $other)
    {
        return $this->sentence === $other->sentence;
    }

    /**
     * @return string
     */
    public function getSentence()
    {
        return $this->sentence;
    }
}
