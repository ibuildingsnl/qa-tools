<?php

namespace Ibuildings\QaTools\Core\IO\File;

interface FileHandler
{
    /**
     * @param string $filePath the path to the file to write to
     * @param string $data the data to write
     * @return void
     */
    public function writeTo($filePath, $data);

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
