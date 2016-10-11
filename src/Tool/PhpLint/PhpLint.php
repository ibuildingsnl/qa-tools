<?php

namespace Ibuildings\QaTools\Tool\PhpLint;

use Ibuildings\QaTools\Core\Tool\AbstractTool;

final class PhpLint extends AbstractTool
{
    const TARGET_NAME_DIFF = 'phplint-diff';
    const TARGET_NAME_FULL = 'phplint-full';
}
