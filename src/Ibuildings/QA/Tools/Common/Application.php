<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common;

use Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    const VERSION = '1.1.17';

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var DialogHelper
     */
    protected $dialog;

    /**
     * @return \Ibuildings\QA\Tools\Common\Settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param $name
     * @param Settings $settings
     */
    public function __construct($name, Settings $settings)
    {
        $this->settings = $settings;
        parent::__construct($name, self::VERSION);
    }

    /**
     * Returns the Dialog helper object.
     *
     * @return DialogHelper
     */
    public function getDialogHelper()
    {
        if ($this->dialog instanceof DialogHelper) {
            return $this->dialog;
        }

        $this->dialog = new DialogHelper();
        $this->dialog->setHelperSet($this->getHelperSet());
        return $this->dialog;
    }
}
