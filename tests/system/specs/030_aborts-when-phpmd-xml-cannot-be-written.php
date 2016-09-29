<?php

namespace Ibuildings\QaTools\SystemTest;

const READ_AND_EXECUTE_PERMISSIONS = 0555;

Composer::initialise();
Composer::install();
chmod('.', READ_AND_EXECUTE_PERMISSIONS);

/** @var callable $expect */
$expect();

assertFileNotExists('qa-tools.json');
Composer::assertPackageIsNotInstalled('phpmd/phpmd');
assertFileNotExists('phpmd.xml');
