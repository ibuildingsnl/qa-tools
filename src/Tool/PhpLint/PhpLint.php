<?php

namespace Ibuildings\QaTools\Tool\PhpLint;

use Ibuildings\QaTools\Core\Tool\AbstractTool;

final class PhpLint extends AbstractTool
{
    const ANT_TARGET_DIFF = 'phplint-diff';
    const ANT_TARGET_FULL = 'phplint-full';
}
