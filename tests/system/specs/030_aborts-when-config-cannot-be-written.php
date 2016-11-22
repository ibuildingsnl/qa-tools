<?php

namespace Ibuildings\QaTools\SystemTest;

const READ_AND_EXECUTE_PERMISSIONS = 0555;
const ALL_PERMISSIONS = 0777;

Composer::initialise();
Composer::install();

/** @var callable $expect */
$expect('setup');

chmod('.', READ_AND_EXECUTE_PERMISSIONS);
$expect('try-writing');

// Allow the directory to be removed after the test has succeeded.
chmod('.', ALL_PERMISSIONS);
