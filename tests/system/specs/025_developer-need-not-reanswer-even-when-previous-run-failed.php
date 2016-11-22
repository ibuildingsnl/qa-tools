<?php

namespace Ibuildings\QaTools\SystemTest;

Composer::initialise();
Composer::addConflict('phpmd/phpmd', '*');

/** @var callable $expect */
$expect('configure');

// Remove conflict with phpmd/phpmd
Composer::initialise();

$expect('reconfigure');

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled('phpmd/phpmd');
assertFileExists('phpmd.xml');
