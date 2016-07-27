<?php

namespace Ibuildings\QaTools\UnitTest\Core\Project;

use Ibuildings\QaTools\Core\Project\ProjectType;
use Ibuildings\QaTools\Core\Project\ProjectTypeSet;
use PHPUnit\Framework\TestCase;

class ProjectTypeSetTest extends TestCase
{
    /**
     * @test
     * @group value
     * @dataProvider unequalSets
     *
     * @param array $set0
     * @param array $set1
     */
    public function it_can_test_for_inequality(array $set0, array $set1)
    {
        $this->assertFalse(
            (new ProjectTypeSet($set0))->equals(new ProjectTypeSet($set1)),
            "ProjectType sets are not equal, but are reported to be"
        );
    }

    public function unequalSets()
    {
        return [
            [
                [new ProjectType(ProjectType::TYPE_PHP_SF_2), new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
            ],
            [
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2), new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
                [new ProjectType(ProjectType::TYPE_PHP_SF_2)],
            ],
            [
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
                [new ProjectType(ProjectType::TYPE_PHP_DRUPAL_7), new ProjectType(ProjectType::TYPE_PHP_DRUPAL_7)],
            ],
            [
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
                [],
            ],
            [
                [],
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
            ],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider equalSets
     *
     * @param array $set0
     * @param array $set1
     */
    public function it_can_test_for_equality(array $set0, array $set1)
    {
        $this->assertTrue(
            (new ProjectTypeSet($set0))->equals(new ProjectTypeSet($set1)),
            "ProjectType sets should be equal, but they're not"
        );
    }

    public function equalSets()
    {
        return [
            [
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2), new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
            ],
            [
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2), new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
            ],
            [
                [],
                [],
            ],
            [
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2), new ProjectType(ProjectType::TYPE_PHP_SF_2)],
                [new ProjectType(ProjectType::TYPE_JS_ANGULAR_2), new ProjectType(ProjectType::TYPE_PHP_SF_2)],
            ],
            [
                [
                    new ProjectType(ProjectType::TYPE_JS_ANGULAR_2),
                    new ProjectType(ProjectType::TYPE_PHP_SF_2),
                    new ProjectType(ProjectType::TYPE_PHP_SF_2),
                ],
                [new ProjectType(ProjectType::TYPE_PHP_SF_2), new ProjectType(ProjectType::TYPE_JS_ANGULAR_2)],
            ],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider setsThatContainAnProjectType
     * @param ProjectTypeSet $set
     * @param ProjectType    $projectType
     */
    public function sets_can_contain_project_types(ProjectTypeSet $set, ProjectType $projectType)
    {
        $this->assertTrue(
            $set->contains($projectType),
            sprintf(
                'Set of %d should contain project type "%s", but ProjectTypeSet reports otherwise',
                count($set),
                $projectType->getProjectType()
            )
        );
    }

    public function setsThatContainAnProjectType()
    {
        return [
            '1-set'         => [
                new ProjectTypeSet([$this->projectTypeDrupal7()]),
                $this->projectTypeDrupal7(),
            ],
            '2-set, first'  => [
                new ProjectTypeSet([$this->projectTypeDrupal7(), $this->projectTypePhpDrupal8()]),
                $this->projectTypeDrupal7(),
            ],
            '2-set, second' => [
                new ProjectTypeSet([$this->projectTypeDrupal7(), $this->projectTypePhpDrupal8()]),
                $this->projectTypePhpDrupal8(),
            ],
        ];
    }

    /**
     * @test
     * @group value
     * @dataProvider setsThatDontContainAnProjectType
     *
     * @param ProjectTypeSet $set
     * @param ProjectType    $projectType
     */
    public function sets_can_not_contain_project_types(ProjectTypeSet $set, ProjectType $projectType)
    {
        $this->assertFalse(
            $set->contains($projectType),
            sprintf(
                'Set of %d should not contain project type "%s", but ProjectTypeSet reports otherwise',
                count($set),
                $projectType->getProjectType()
            )
        );
    }

    public function setsThatDontContainAnProjectType()
    {
        return [
            '2-set' => [
                new ProjectTypeSet([$this->projectTypeDrupal7(), $this->projectTypePhpDrupal8()]),
                $this->projectTypePhpOther(),
            ],
            '0-set' => [
                new ProjectTypeSet([]),
                $this->projectTypeDrupal7(),
            ],
            '1-set' => [
                new ProjectTypeSet([$this->projectTypeDrupal7()]),
                $this->projectTypePhpDrupal8(),
            ],
        ];
    }

    /**
     * @return ProjectType
     */
    private function projectTypeDrupal7()
    {
        return new ProjectType(ProjectType::TYPE_PHP_DRUPAL_7);
    }

    /**
     * @return ProjectType
     */
    private function projectTypePhpDrupal8()
    {
        return new ProjectType(ProjectType::TYPE_PHP_DRUPAL_8);
    }

    /**
     * @return ProjectType
     */
    private function projectTypePhpOther()
    {
        return new ProjectType(ProjectType::TYPE_PHP_OTHER);
    }
}
