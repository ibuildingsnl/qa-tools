<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Tool\PhpCs\PhpCs;
use Ibuildings\QaTools\Tool\PhpLint\PhpLint;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

Composer::initialise();

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled('phpmd/phpmd');

assertFileExists('build.xml');
assertFileContains('build.xml', PhpCs::ANT_TARGET);
assertFileContains('build.xml', PhpMd::ANT_TARGET);
assertFileContains('build.xml', PhpLint::ANT_TARGET_FULL);
assertFileContains('build.xml', PhpLint::ANT_TARGET_DIFF);

assertFileExists('phpmd.xml');
assertFileContains('phpmd.xml', 'Ibuildings QA Tools Default Ruleset');

assertFileExists('ruleset.xml');
Composer::assertPackageIsInstalled('squizlabs/php_codesniffer');