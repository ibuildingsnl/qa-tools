<?php

namespace Ibuildings\QaTools\Core\IO\File;

interface FileHandler
{
    /**
     * @param string $data     the data to write
     * @param string $filePath the path to the file to write to
     * @return void
     */
    public function writeTo($data, $filePath);

    /**
     * @param string $filePath the path to the file to read the contents of
     * @return string
     */
    public function readFrom($filePath);

    /**
     * @param string $filePath the path to the file to remove
     * @return void
     */
    public function remove($filePath);

    /**
     * @param string $filePath
     * @return boolean
     */
    public function exists($filePath);
}
