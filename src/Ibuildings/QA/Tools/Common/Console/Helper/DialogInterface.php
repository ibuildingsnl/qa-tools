<?php

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
