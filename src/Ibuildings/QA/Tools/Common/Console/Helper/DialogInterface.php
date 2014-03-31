<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Console\Helper;

/**
 * Dialog interface. Implement this interface to have the dialog class injected.
 */
interface DialogInterface
{
    /**
     * Sets the helper.
     *
     * @param DialogHelper $helper
     */
    public function setDialogHelper(DialogHelper $helper);
}
