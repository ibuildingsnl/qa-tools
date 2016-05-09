<?php

namespace Ibuildings\QaTools\IO\File;

interface FileHandler
{
    /**
     * @param mixed  $data     the data to write
     * @param string $filePath the path to the file to write to
     * @return void
     */
    public function writeTo($data, $filePath);

    /**
     * @param string $filePath the path to the file to read the contents of
     * @return mixed
     */
    public function readFrom($filePath);

    /**
     * @param string $filePath the path to the file to remove
     * @return void
     */
    public function remove($filePath);
}
