<?php

namespace Ibuildings\QaTools\Core\Requirement\Executor;

use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Requirement\Requirement;
use Ibuildings\QaTools\Core\Requirement\RequirementList;

/**
 * Executes its supported requirements.
 *
 * Users of Executors must guarantee they call their methods in the following order:
 *  - checkPrerequisites()
 *  - execute()
 *  - cleanUp()
 *
 * After the Executor has been execute()d, its rollBack() method may be called. Its
 * rollBack() method is never called directly after checkPrerequisites(). It may be
 * called after cleanUp().
 */
interface Executor
{
    /**
     * @param Requirement $requirement
     * @return bool
     */
    public function supports(Requirement $requirement);

    /**
     * @param RequirementList $requirements
     * @param Interviewer     $interviewer
     * @return void
     */
    public function checkPrerequisites(RequirementList $requirements, Interviewer $interviewer);

    /**
     * @param RequirementList $requirements
     * @param Interviewer     $interviewer
     * @return void
     */
    public function execute(RequirementList $requirements, Interviewer $interviewer);

    /**
     * @param RequirementList $requirements
     * @param Interviewer     $interviewer
     * @return void
     */
    public function cleanUp(RequirementList $requirements, Interviewer $interviewer);

    /**
     * @param RequirementList $requirements
     * @param Interviewer     $interviewer
     * @return void
     */
    public function rollBack(RequirementList $requirements, Interviewer $interviewer);
}
