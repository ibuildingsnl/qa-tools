<?php

namespace Ibuildings\QaTools\UnitTest\Core\Requirement;

use Ibuildings\QaTools\Core\Requirement\EqualsSpecification;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\RequirementList;
use Mockery;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Requirement
 */
class RequirementListTest extends TestCase
{
    /**
     * @test
     */
    public function requirementlist_can_be_filtered_according_to_a_predicate_resulting_in_a_requirementlist_of_the_filtered_requirements()
    {
        $requirementMockThatShouldRemain = Mockery::mock(Requirement::class);
        $requirementMockThatShouldRemain->shouldReceive('remains')
            ->andReturn(true);

        $requirementMockThatShouldNotRemain = Mockery::mock(Requirement::class);
        $requirementMockThatShouldNotRemain->shouldReceive('remains')
            ->andReturn(false);

        $requirementList = new RequirementList([$requirementMockThatShouldRemain, $requirementMockThatShouldNotRemain]);
        $expectedFilterResult = new RequirementList([$requirementMockThatShouldRemain]);

        $actualFilterResult = $requirementList->filter(function (Requirement $requirement) {
            return $requirement->remains();
        });

        $this->assertEquals($expectedFilterResult, $actualFilterResult);
    }

    /**
     * @test
     */
    public function two_requirementlists_with_overlapping_requirements_are_merged_resulting_in_one_requirementlist()
    {
        $fakeRequirementA = new FooRequirement('A');
        $fakeRequirementB = new FooRequirement('B');

        $requirementListA = new RequirementList([$fakeRequirementA]);
        $requirementListB = new RequirementList([$fakeRequirementA, $fakeRequirementB]);

        $expectedMergeResult = new RequirementList([$fakeRequirementA, $fakeRequirementB]);

        $actualMergeResult = $requirementListA->merge($requirementListB);

        $this->assertEquals($expectedMergeResult, $actualMergeResult);
    }

    /**
     * @test
     */
    public function two_requirementlists_without_overlapping_requirements_are_merged_resulting_in_one_requirementlist()
    {
        $fakeRequirementA = new FooRequirement('A');
        $fakeRequirementB = new FooRequirement('B');

        $requirementListA = new RequirementList([$fakeRequirementA]);
        $requirementListB = new RequirementList([$fakeRequirementB]);

        $expectedMergeResult = new RequirementList([$fakeRequirementA, $fakeRequirementB]);

        $actualMergeResult = $requirementListA->merge($requirementListB);

        $this->assertEquals($expectedMergeResult, $actualMergeResult);
    }

    /**
     * @test
     */
    public function a_requirement_is_added_to_a_requirementlist()
    {
        $requirement = new FooRequirement('A');
        $requirementList = new RequirementList([]);

        $appendedRequirementList = $requirementList->add($requirement);

        $this->assertFalse($requirementList->contains($requirement));
        $this->assertTrue($appendedRequirementList->contains($requirement));
    }

    /**
     * @test
     */
    public function requirementlist_with_two_requirements_counts_as_two()
    {
        $fakeRequirementA = new FooRequirement('A');
        $fakeRequirementB = new FooRequirement('B');

        $requirementList = new RequirementList([$fakeRequirementA, $fakeRequirementB]);

        $this->assertEquals(2, count($requirementList));
    }

    /**
     * @test
     */
    public function requirementlist_with_no_requirements_counts_as_zero()
    {
        $requirementList = new RequirementList([]);

        $this->assertEquals(0, count($requirementList));
    }

    /**
     * @test
     */
    public function requirementlist_is_iterable()
    {
        $fakeRequirementA = new FooRequirement('A');
        $fakeRequirementB = new FooRequirement('B');

        $requirementList = new RequirementList([$fakeRequirementA, $fakeRequirementB]);

        foreach ($requirementList as $requirement) {
            $this->assertTrue($requirement->equals($fakeRequirementA) || $requirement->equals($fakeRequirementB));
        }
    }
}
