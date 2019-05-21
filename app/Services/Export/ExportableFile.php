<?php

namespace App\Services\Export;

interface ExportableFile
{
    /**
     * Gets filename (with extension)
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * Gets file's content
     *
     * @return string
     */
    public function getFileContent(): string;
}
