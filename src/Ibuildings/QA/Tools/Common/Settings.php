<?php

namespace Ibuildings\QA\Tools\Common;

class Settings extends \ArrayObject
{
    /**
     * @return array
     */
    public function toArray()
    {
        return (array)$this;
    }
}