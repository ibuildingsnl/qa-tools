<?php

namespace Ibuildings\QaTools\SystemTest;

Composer::initialise();

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled('phpmd/phpmd');
assertFileExists('phpmd.xml');
assertFileContains('phpmd.xml', 'Ibuildings QA Tools Default Ruleset');

assertFileExists('ruleset.xml');
Composer::assertPackageIsInstalled('squizlabs/php_codesniffer');
