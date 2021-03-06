<?php

namespace Ibuildings\QaTools\SystemTest;

Composer::initialise();
Composer::addConflict('phpmd/phpmd', '*');

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');
Composer::assertPackageIsNotInstalled('phpmd/phpmd');
assertFileNotExists('phpmd.xml');
