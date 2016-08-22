<?php

namespace Ibuildings\QaTools\Core\Interviewer;

interface ScopedInterviewer extends Interviewer
{
    /**
     * Sets the scope in which questions, asked through {Interviewer#ask()},
     * are asked.
     *
     * @param string $scope
     * @return void
     */
    public function setScope($scope);
}
