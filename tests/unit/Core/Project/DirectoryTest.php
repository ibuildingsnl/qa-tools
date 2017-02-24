<?php

namespace Ibuildings\QaTools\UnitTest\Core\Project;

use Ibuildings\QaTools\Core\Project\Directory;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Ibuildings\QaTools\UnitTest\Diffing;

/**
 * @group Project
 * @group Directory
 */
class DirectoryTest extends MockeryTestCase
{
    use Diffing;

    /** @test */
    public function wrap_a_directory()
    {
        $directoryString = './';
        $directory = new Directory($directoryString);

        $this->assertSame($directoryString, $directory->getDirectory());
    }

    /**
     * @test
     * @dataProvider equalDirectories
     * @param string $a
     * @param string $b
     */
    public function can_equal_another_directory($a, $b)
    {
        $dirA = new Directory($a);
        $dirB = new Directory($b);

        $this->assertTrue(
            $dirA->equals($dirB),
            $this->diff($a, $b, 'Directories were expected to equal')
        );
    }

    public function equalDirectories()
    {
        return [
            '/abc == /abc'                 => ['/abc', '/abc'],
            './ == ./'                     => ['./', './'],
            'vendor/. == vendor'           => ['vendor/.', 'vendor'],
            './vendor == vendor'           => ['./vendor', 'vendor'],
            'rel\\ative == rel/ative'      => ['rel\\ative', 'rel/ative'],
            './vendor/./psr == vendor/psr' => ['./vendor/./psr', 'vendor/psr'],
            '/./abc == /abc/./'            => ['/./abc', '/abc/./'],
            '/./abc/.. == /abc/./..'       => ['/./abc/..', '/abc/./../'],
        ];
    }

    /** @test */
    public function can_not_equal_another_directory()
    {
        $this->assertFalse((new Directory(' ./'))->equals(new Directory(' ./vendor')));
        $this->assertFalse((new Directory(' ../'))->equals(new Directory(' ./')));
    }

    /** @test */
    public function a_relative_directory_can_be_subtracted()
    {

    }
}
