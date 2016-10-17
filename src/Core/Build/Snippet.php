<?php
namespace Ibuildings\QaTools\Core\Build;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class Snippet
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @var string
     */
    private $target;

    /**
     * @param string $contents
     * @param string $target
     * @return Snippet
     */
    public static function withContentsAndTargetName($contents, $target)
    {
        return new self($contents, $target);
    }

    /**
     * @param string $contents
     * @param string $target
     */
    private function __construct($contents, $target)
    {
        Assertion::string(
            $contents,
            sprintf('contents should be string but is %s', gettype($contents))
        );
        Assertion::string(
            $target,
            sprintf('target should be string but is %s', gettype($target))
        );

        $this->contents = $contents;
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param Snippet $other
     * @return bool
     */
    public function equals(Snippet $other)
    {
        return $this->target === $other->target
            && $this->contents === $other->contents;
    }
}
