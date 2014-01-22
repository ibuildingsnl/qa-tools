<?php

namespace Ibuildings\QA\Tools\Common;

use Ibuildings\QA\Tools\Common\Console\Helper\DialogHelper;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
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
     * @param $version
     * @param Settings $settings
     */
    public function __construct($name, $version, Settings $settings)
    {
        $this->settings = $settings;
        parent::__construct($name, $version);
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
        return $this->dialog;
    }
}