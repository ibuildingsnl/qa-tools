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
     * Verifies that data can be written to the given file path, keeping a backup on
     * the side.
     *
     * @param string $filePath the path to the file to write to
     * @return bool
     */
    public function canWriteWithBackupTo($filePath);

    /**
     * Writes the data to the given file path, keeping a backup on the side.
     *
     * @param string $filePath the path to the file to write to
     * @param string $data the data to write
     * @return void
     */
    public function writeWithBackupTo($filePath, $data);

    /**
     * Restores the backup created for the given file path written in
     * {writeWithBackupTo()}.
     *
     * @param string $filePath the path to the file of which to remove the backup
     * @return void
     */
    public function restoreBackupOf($filePath);

    /**
     * Discards the backup of the given file path. Such a backup is created when using
     * {writeWithBackupTo()}.
     *
     * @param string $filePath the path to the file of which to discard the backup
     * @return void
     */
    public function discardBackupOf($filePath);

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

    /**
     * @param string $filePath
     * @param int $mode
     * @return mixed
     */
    public function changeMode($filePath, $mode);
}
