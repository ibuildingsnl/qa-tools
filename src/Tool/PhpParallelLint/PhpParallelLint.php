<?php

namespace Ibuildings\QaTools\Tool\PhpParallelLint;

use Ibuildings\QaTools\Core\Tool\AbstractTool;

final class PhpParallelLint extends AbstractTool
{
    const ANT_TARGET_DIFF = 'php-parallel-lint-diff';
    const ANT_TARGET_FULL = 'php-parallel-lint-full';
}
