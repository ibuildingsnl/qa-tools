<?php

namespace Ibuildings\QaTools\SystemTest;

/** @var callable $expect */
$expect();

assertFileExists('.git/hooks/pre-commit');
