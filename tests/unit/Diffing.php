<?php

namespace Ibuildings\QaTools\UnitTest;

use Ibuildings\QaTools\Core\Assert\Assertion;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Exporter\Exporter;

trait Diffing
{
    /**
     * Produces a diff between to arbitrary values, preceded by the given title,
     * using the same mechanism PHPUnit uses.
     *
     * Example usage:
     *
     *     use Diffing;
     *     $this->assertTrue($a->equals($b), $this->diff($a, $b, 'A ought to equal B'));
     *
     * @param mixed $expected
     * @param mixed $actual
     * @param string $title
     * @return string
     */
    protected function diff($expected, $actual, $title)
    {
        Assertion::string($title, 'Diff title ought to be a string, got "%s" of type "%s"');

        $differ = new Differ();
        $exporter = new Exporter();

        $diff = $differ->diff($exporter->export($expected), $exporter->export($actual));

        return sprintf("%s\n\n%s\n", $title, $diff);
    }
}
