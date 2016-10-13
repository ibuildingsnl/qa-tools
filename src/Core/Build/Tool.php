<?php
namespace Ibuildings\QaTools\Core\Build;

use Ibuildings\QaTools\Core\Assert\Assertion;

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
        Assertion::string(
            $toolIdentifier,
            sprintf('toolIdentifier should be string but is %s', gettype($toolIdentifier))
        );
        $this->toolIdentifier = $toolIdentifier;
    }

    /**
     * @param string $toolIdentifier
     * @return Tool
     */
    public static function withIdentifier($toolIdentifier)
    {
        return new self($toolIdentifier);
    }

    /**
     * @param Tool     $other
     * @param string[] $toolOrder
     * @return int
     */
    public function compare(Tool $other, array $toolOrder)
    {
        $otherIndex = array_search($other->toolIdentifier, $toolOrder);
        $thisIndex = array_search($this->toolIdentifier, $toolOrder);

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
