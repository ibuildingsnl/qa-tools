<?php
namespace Ibuildings\QaTools\Core\Build;

class Tool
{
    /**
     * @var string
     */
    private $toolIdentifier;

    /**
     * @param string $toolIdentifier
     */
    private function __construct($toolIdentifier)
    {
        $this->toolIdentifier = $toolIdentifier;
    }

    /**
     * @param $toolIdentifier
     * @return Tool
     */
    public static function withIdentifier($toolIdentifier)
    {
        return new self($toolIdentifier);
    }

    /**
     * @param Tool $other
     * @param array $priorities
     * @return int
     */
    public function compare(Tool $other, array $priorities)
    {
        $otherIndex = array_search($other->toolIdentifier, $priorities);
        $thisIndex = array_search($this->toolIdentifier, $priorities);

        if ($otherIndex === $thisIndex) {
            return 0;
        }

        if ($otherIndex > $thisIndex) {
            return -1;
        }

        return 1;
    }

    /**
     * @param Tool $other
     * @return bool
     */
    public function equals(Tool $other)
    {
        return $this->toolIdentifier == $other->toolIdentifier;
    }
}
