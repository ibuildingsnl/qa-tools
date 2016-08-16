<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Ibuildings\QaTools\Core\Task\Composer\Package;
use Ibuildings\QaTools\Core\Task\RequireComposerPackagesTask;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @group Task
 */
class RequireComposerPackagesTaskTest extends TestCase
{
    /**
     * @test
     * @dataProvider equalTasks
     * @param RequireComposerPackagesTask $a
     * @param RequireComposerPackagesTask $b
     */
    public function can_be_equal(RequireComposerPackagesTask $a, RequireComposerPackagesTask $b)
    {
        $this->assertTrue($a->equals($b), 'Tasks ought to equal each other');
    }

    public function equalTasks()
    {
        return [
            '[c/c:dev-master] == [c/c:dev-master]' => [new RequireComposerPackagesTask(new Package('c/c', 'dev-master')), new RequireComposerPackagesTask(new Package('c/c', 'dev-master'))],
            '[a/a:1, b/b:2] == [b/b:2, a/a:1]' => [new RequireComposerPackagesTask(new Package('a/a', '1'), new Package('b/b', '2')), new RequireComposerPackagesTask(new Package('b/b', '2'), new Package('a/a', '1'))],
        ];
    }

    /**
     * @test
     * @dataProvider unequalTasks
     * @param RequireComposerPackagesTask $a
     * @param RequireComposerPackagesTask $b
     */
    public function can_not_be_equal(RequireComposerPackagesTask $a, RequireComposerPackagesTask $b)
    {
        $this->assertFalse($a->equals($b), 'Tasks ought not to equal each other');
    }

    public function unequalTasks()
    {
        return [
            '[c/c:dev-master] != [a/a:1]' => [new RequireComposerPackagesTask(new Package('c/c', 'dev-master')), new RequireComposerPackagesTask(new Package('a/a', '1'))],
            '[a/a:1] != [b/b:2]' => [new RequireComposerPackagesTask(new Package('a/a', '1')), new RequireComposerPackagesTask(new Package('b/b', '2'))],
        ];
    }
}
