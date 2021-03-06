<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Tool\Behat\Behat;
use Ibuildings\QaTools\Tool\PhpCs\PhpCs;
use Ibuildings\QaTools\Tool\PhpParallelLint\PhpParallelLint;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Ibuildings\QaTools\Tool\SensioLabsSecurityChecker\SensioLabsSecurityChecker;

Composer::initialise();

/** @var callable $expect */
$expect();

Composer::assertPackageIsInstalled('phpunit/phpunit');
assertFileExists('phpunit.xml');

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled('phpmd/phpmd');

assertFileExists('build.xml');
assertFileContains('build.xml', PhpCs::ANT_TARGET);
assertFileContains('build.xml', PhpMd::ANT_TARGET);
assertFileContains('build.xml', PhpParallelLint::ANT_TARGET_FULL);
assertFileContains('build.xml', PhpParallelLint::ANT_TARGET_DIFF);
assertFileContains('build.xml', SensioLabsSecurityChecker::ANT_TARGET);
assertFileContains('build.xml', Behat::ANT_TARGET);

assertFileExists('phpmd.xml');
assertFileContains('phpmd.xml', 'Ibuildings QA Tools Default Ruleset');

assertFileExists('ruleset.xml');
Composer::assertPackageIsInstalled('squizlabs/php_codesniffer');
Composer::assertPackageIsInstalled('behat/behat');
Composer::assertPackageIsInstalled('drupal/drupal-extension');
