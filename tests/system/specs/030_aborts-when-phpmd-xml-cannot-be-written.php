<?php

namespace Ibuildings\QaTools\SystemTest;

const READ_AND_EXECUTE_PERMISSIONS = 0555;
const ALL_PERMISSIONS = 0777;

Composer::initialise();
Composer::install();
chmod('.', READ_AND_EXECUTE_PERMISSIONS);

/** @var callable $expect */
$expect();

assertFileNotExists('qa-tools.json');
Composer::assertPackageIsNotInstalled('phpmd/phpmd');
assertFileNotExists('phpmd.xml');

// Allow the directory to be removed after the test has succeeded.
chmod('.', ALL_PERMISSIONS);
